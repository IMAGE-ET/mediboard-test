<?php

/**
 * ITI31 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI31DelegatedHandler 
 * ITI31 Delegated Handler
 */
class CITI31DelegatedHandler extends CITIDelegatedHandler {
  static $handled        = array ("CSejour", "CAffectation", "CNaissance");
  protected $profil      = "PAM";
  protected $transaction = "ITI31";

  static $inpatient      = array("comp", "ssr", "psy", "seances", "consult", "ambu");
  static $outpatient     = array("urg", "exte");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);  

    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      $sejour->loadRefPatient();
      
      // Si on ne souhaite explicitement pas de synchro
      if ($sejour->_no_synchro) {
        return;
      }
      
      // Si on est en train de créer un séjour et qu'il s'agit d'une naissance
      $current_log = $sejour->loadLastLog();
      if ($current_log->type == "create" && $sejour->_naissance) {
        return;
      }
      
      // Si Serveur
      if (CAppUI::conf('smp server')) {} 
      // Si Client
      else {
        $code = $this->getCodeSejour($sejour);
        
        // Cas où : 
        // * on est l'initiateur du message 
        // * le destinataire ne supporte pas le message
        if ($sejour->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $code, $receiver)) {
          return;
        }
        
        if (!$sejour->_NDA) {
          // Génération du NDA dans le cas de la création, ce dernier n'était pas créé
          if ($msg = $sejour->generateNDA()) {
            CAppUI::setMsg($msg, UI_MSG_ERROR);
          }
          
          $NDA = new CIdSante400();
          $NDA->loadLatestFor($sejour, $receiver->_tag_sejour);
          $sejour->_NDA = $NDA->id400;
        }
        
        $current_affectation = null;
        // Cas où lors de l'entrée réelle j'ai une affectation qui n'a pas été envoyée
        if ($sejour->fieldModified("entree_reelle") && !$sejour->_old->entree_reelle) {
          $current_affectation = $sejour->getCurrAffectation();
        }

        $this->createMovement($code, $sejour, $current_affectation);

        // Envoi de l'événement
        $this->sendITI($this->profil, $this->transaction, $code, $sejour);
      }
    }
    
    // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;
      $current_log = $affectation->_ref_current_log;

      if (!$current_log || $affectation->_no_synchro || !in_array($current_log->type, array("create", "store"))) {
        return;
      }
      
      // On envoie pas les affectations prévisionnelles 
      $sejour = $affectation->loadRefSejour();
      if (!$sejour->_id || $sejour->_etat == "preadmission") {
        return;
      }
      $first_affectation = $sejour->loadRefFirstAffectation();

      $code = $this->getCodeAffectation($affectation, $first_affectation);

      // Cas où : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($affectation->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $code, $receiver)) {
        return;
      }
      
      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;
      $this->createMovement($code, $sejour, $affectation);
   
      // Envoi de l'événement
      $this->sendITI($this->profil, $this->transaction, $code, $sejour);
    }
    
    // Traitement Naissance
    if ($mbObject instanceof CNaissance) {
      $current_log = $mbObject->loadLastLog();
      if ($current_log->type != "create") {
        return;
      }

      $sejour_enfant = $mbObject->loadRefSejourEnfant();
      $sejour_enfant->loadRefPatient();
      $sejour_enfant->_receiver = $receiver;
      
      $code = $this->getCodeSejour($sejour_enfant);
        
      // Cas où : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($sejour_enfant->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $code, $receiver)) {
        return;
      }
      
      if (!$sejour_enfant->_NDA) {
        // Génération du NDA dans le cas de la création, ce dernier n'était pas créé
        if ($msg = $sejour_enfant->generateNDA()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
        
        $NDA = new CIdSante400();
        $NDA->loadLatestFor($sejour_enfant, $receiver->_tag_sejour);
        $sejour_enfant->_NDA = $NDA->id400;
      }
      
      $current_affectation = null;
      // Cas où lors de l'entrée réelle j'ai une affectation qui n'a pas été envoyée
      if ($sejour_enfant->fieldModified("entree_reelle") && !$sejour_enfant->_old->entree_reelle) {
        $current_affectation = $sejour_enfant->getCurrAffectation();
      }

      $this->createMovement($code, $sejour_enfant, $current_affectation);

      // Envoi de l'événement
      $this->sendITI($this->profil, $this->transaction, $code, $sejour_enfant);
    }
  }
  
  function createMovement($code, CSejour $sejour, CAffectation $affectation = null) {
    $insert = in_array($code, CHL7v2SegmentZBE::$actions["INSERT"]);
    $update = in_array($code, CHL7v2SegmentZBE::$actions["UPDATE"]);
    $cancel = in_array($code, CHL7v2SegmentZBE::$actions["CANCEL"]);

    $movement = new CMovement();
    // Initialise le mouvement 
    $movement->sejour_id     = $sejour->_id;
  
    if ($affectation) {
      $movement->affectation_id = $affectation->_id;  
    }

    if ($insert) {
      // Dans le cas d'un insert le type correspond nécessairement au type actuel du séjour
      $movement->movement_type         = $sejour->getMovementType($code);
      $movement->original_trigger_code = $code;
      $movement->start_of_movement     = $this->getStartOfMovement($code, $sejour, $affectation);
      $movement->store();
    
      return $sejour->_ref_hl7_movement = $movement;
    }
    elseif ($update) {
      // Dans le cas d'un update le type correspond à celui du trigger
      $movement_type = $sejour->getMovementType($code);

      // Mise à jour entrée réelle
      if ($sejour->fieldModified("entree_reelle")) {
        $movement_type = "ADMI";
      }
      
      // Mise à jour sortie réelle
      if ($sejour->fieldModified("sortie_reelle")) {
        $movement_type = "SORT";
      }

      // Mise à jour d'une affectation
      if ($affectation && $affectation->_ref_current_log->type == "store") {
        $movement_type = "MUTA";
      }
      $movement->movement_type = $movement_type;
    }

    $order = "affectation_id DESC";
    
    $movements = $movement->loadMatchingList($order);    
    if (!empty($movements)) {
      $movement = reset($movements);
    }
    
    if ($update) {
      $movement->start_of_movement = $this->getStartOfMovement($movement->original_trigger_code, $sejour, $affectation);
    }

    if ($cancel) {
      $movement->cancel = 1;
    }
    $movement->store();
    
    return $sejour->_ref_hl7_movement = $movement;
  }

  function getStartOfMovement($code, CSejour $sejour, CAffectation $affectation = null) {
    switch ($code) {
      // Admission hospitalisé / externe
      case 'A01' : 
      case 'A04' :
        // Date de l'admission
        return $sejour->entree_reelle;
      // Mutation : changement d'UF hébergement
      case 'A02':
        return $affectation->entree;
      // Changement de statut externe ou urgence vers hospitalisé
      case 'A06':
      // Changement de statut hospitalisé ou urgence vers externe
      case 'A07':
      // Absence provisoire (permission) et mouvement de transfert vers un plateau technique pour acte (<48h)
      case 'A21':
      // Retour d'absence provisoire (permission) et mouvement de transfert vers un plateau technique pour acte (<48h)
      case 'A22': 
      // Changement de médecin responsable
      case 'A54':
      // Changement d'UF médicale
      case 'Z80':
      // Changement d'UF de soins
      case 'Z84':
        // Date du transfert
        return mbDateTime();
      // Sortie définitive
      case 'A03':
        // Date de la sortie
        return $sejour->sortie_reelle;
      // Pré-admission
      case 'A05':
        // Date de la pré-admission
        return $sejour->entree_prevue;
      // Sortie en attente
      case 'A16':
        // Date de la sortie
        return $sejour->sortie;
    }
  }
  
  function getCodeSejour(CSejour $sejour) {
    $current_log = $sejour->loadLastLog();
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $receiver = $sejour->_receiver;
    $configs  = $receiver->_configs;
        
    $sejour->loadOldObject();
    // Cas d'une pré-admission
    if ($sejour->_etat == "preadmission") {
      // Création d'une pré-admission
      if ($current_log->type == "create") {
        return "A05";
      } 
      // Modification d'une pré-admission
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A38";
      }
      
      // Cas d'un rétablissement d'annulation ? 
      if ($sejour->fieldModified("annule", "0") && ($sejour->_old->annule == 1)) {
        return "A05";
      }

      // Annulation de l'admission
      if ($sejour->_old->entree_reelle && !$sejour->entree_reelle) {
        return "A11";
      }
      
      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }
    
    // Cas d'un séjour en cours (entrée réelle)
    if ($sejour->_etat == "encours") {
      // Admission faite
      if ($sejour->fieldModified("entree_reelle") && !$sejour->_old->entree_reelle ||
          $sejour->entree_reelle && !$sejour->_old->entree_reelle) {
        // Patient externe
        if (in_array($sejour->type, self::$outpatient)) {
          return "A04";
        } 
        // Admission hospitalisé
        return "A01";
      }
      
      // Confirmation de sortie
      if ($sejour->fieldModified("confirme", "1")) {
        return "A16";
      }
      
      // Annulation confirmation de sortie
      if ($sejour->_old->confirme && $sejour->fieldModified("confirme", "0")) {
        return "A25";
      }
      
      // Bascule externe devient hospitalisé (outpatient > inpatient)
      if ($sejour->fieldModified("type") 
        && (in_array($sejour->type, self::$inpatient)) 
        && in_array($sejour->_old->type, self::$outpatient)) {
        return "A06";
      }
      
      // Bascule d'hospitalisé à externe (inpatient > outpatient)
      if ($sejour->fieldModified("type") 
        && (in_array($sejour->type, self::$outpatient)) 
        && in_array($sejour->_old->type, self::$inpatient)) {
        return "A07";
      }
      
      // Annulation du médecin responsable
      $send_change_attending_doctor = $configs["send_change_attending_doctor"];
      if ($sejour->fieldModified("praticien_id") && 
         ($sejour->praticien_id != $sejour->_old->praticien_id)) {
        return (($send_change_attending_doctor == "A54") ? "A55" : $this->getModificationAdmitCode($receiver));
      } 
      
      // Changement du médecin responsable
      if ($sejour->fieldModified("praticien_id")) {
        return (($send_change_attending_doctor == "A54") ? "A54" : $this->getModificationAdmitCode($receiver));
      } 
      
      // Réattribution dossier administratif
      if ($sejour->fieldModified("patient_id")) {
        return "A44";
      }

      // Cas d'une annulation
      if ($sejour->fieldModified("annule", "1")) {
        return "A11";
      }
      
      // Annulation de la sortie réelle
      if ($sejour->_old->sortie_reelle && !$sejour->sortie_reelle) {
        return "A13";
      }
      
      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }
    
    // Cas d'un séjour clôturé (sortie réelle)
    if ($sejour->_etat == "cloture") {
      // Sortie réelle renseignée
      if ($sejour->fieldModified("sortie_reelle") && !$sejour->_old->sortie_reelle) {
        return "A03";
      }
      
      // Modification de l'admission
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A13";
      }
      
      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }
  }
  
  function getCodeAffectation(CAffectation $affectation, CAffectation $first_affectation = null) {
    $current_log = $affectation->_ref_current_log;
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $receiver = $affectation->_receiver;
    $configs  = $receiver->_configs;

    if ($current_log->type == "create") {
      // Dans le cas où il s'agit de la première affectation du séjour on ne fait pas une mutation mais une modification
      if ($first_affectation && ($first_affectation->_id == $affectation->_id)) {
        return $this->getModificationAdmitCode($receiver);
      }
      
      // Création d'une affectation
      return "A02";
    }
    
    /* Affectation dans un service externe */
    $service = $affectation->loadRefService();
    if ($service->externe && $affectation->effectue) {
      return "A21";
    }
            
    /* Affectation dans un service externe effectuée */
    if ($service->externe && $affectation->_old->effectue && !$affectation->effectue) {
      return "A22";
    }
    
    $send_change_medical_responsibility = $configs["send_change_medical_responsibility"];
    /* Changement d'UF Médicale */
    if ($affectation->_old->uf_medicale_id && $affectation->fieldModified("uf_medicale_id")) {
      /* @todo Gérer le cas où : création d'une nouvelle affectation && UM1 # UM2 */
      switch ($configs["send_change_medical_responsibility"]) {
        case 'Z80':
          return "Z80";
        case 'A02':
          return "A02";
        default:
         return $this->getModificationAdmitCode($receiver);
      }
    }

    /* Changement d'UF de Soins */
    if ($affectation->_old->uf_soins_id && $affectation->fieldModified("uf_soins_id")) {
      /* @todo Gérer le cas où : création d'une nouvelle affectation && US1 # US2 */
      switch ($configs["send_change_nursing_ward"]) {
        case 'Z84':
          return "Z84";
        case 'A02':
          return "A02";
        default:
         return $this->getModificationAdmitCode($receiver);
      }
    }
 
    // Modifcation d'une affectation
    return $this->getModificationAdmitCode($affectation->_receiver);
  }
  
  function getModificationAdmitCode(CReceiverIHE $receiver) {
    switch ($receiver->_i18n_code) {
      // Cas de l'extension française : Z99
      case "FR" :
        $code = "Z99";
        break;
      // Cas internationnal : A08
      default :
        $code = "A08";
        break;
    }
    
    return $code;
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }  
  
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);  

    // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;
      $current_log = $affectation->_ref_current_log;
      if ($affectation->_no_synchro) {
        return;
      }
      
      $sejour = $affectation->loadRefSejour();
      if (!$sejour->_id || $sejour->_etat == "preadmission") {
        return;
      }

      /* Annulation de l'affectation dans un service externe */
      $service = $affectation->loadRefService();
      if ($service->externe) {
        // Affectation effectuée 
        if ($affectation->effectue) {
          $code = "A52";
        }
        // Affectation non effectuée
        else {
          $code = "A53";
        }
      }
      
      // Annulation d'une affectation
      else {
        $code = "A12";
      }
      
      // Cas où : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($affectation->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $code, $receiver)) {
        return;
      }
            
      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;

      $this->createMovement($code, $sejour, $affectation);
   
      // Envoi de l'événement
      $this->sendITI($this->profil, $this->transaction, $code, $sejour);
    }  
  }  
  
  function onAfterDelete(CMbObject $mbObject) {
  }
}
?>