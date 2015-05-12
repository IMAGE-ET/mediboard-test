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
  /**
   * @var array
   */
  static $handled        = array ("CSejour", "CAffectation", "CNaissance");
  /**
   * @var string
   */
  protected $profil      = "PAM";
  /**
   * @var string
   */
  protected $message     = "ADT";
  /**
   * @var string
   */
  protected $transaction = "ITI31";

  /**
   * @var array
   */
  static $inpatient      = array("comp", "ssr", "psy", "seances", "consult", "ambu");
  /**
   * @var array
   */
  static $outpatient     = array("urg", "exte");

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    /** @var CInteropReceiver $receiver */
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
      if (CAppUI::conf('smp server')) {
        return;
      }

      // Si le group_id du séjour est différent de celui du destinataire
      if ($sejour->group_id != $receiver->group_id) {
        return;
      }

      // On ne transmet pas les séjours non facturables si le destinataire ne le souhaite pas
      if (!$receiver->_configs["send_no_facturable"] && !$sejour->facturable) {
        return;
      }

      // Passage du séjour d'urgence en hospit, pas de génération de A06
      if ($sejour->_en_mutation) {
        return;
      }

      // Si on ne gère les séjours du bébé on ne transmet pas séjour si c'est un séjour enfant
      if (!$receiver->_configs["send_child_admit"]) {
        $naissance = new CNaissance();
        $naissance->sejour_enfant_id = $sejour->_id;
        $naissance->loadMatchingObject();
        if ($naissance->_id) {
          return;
        }
      }

      // Recherche si on est sur un séjour de mutation
      $rpu = new CRPU();
      $rpu->mutation_sejour_id = $sejour->_id;
      $rpu->loadMatchingObject();

      if ($rpu->_id) {
        $sejour_rpu = $rpu->loadRefSejour();
        if (!$sejour->_cancel_hospitalization && $sejour_rpu->mode_sortie != "mutation") {
          return;
        }
      }

      $current_affectation = null;
      $code                = null;

      // Cas où :
      // * on est sur un séjour d'urgences qui n'est pas le relicat
      // * on est en train de réaliser la mutation
      /** @var CRPU $rpu */
      $rpu  = $sejour->loadRefRPU();
      if ($rpu && $rpu->_id && $rpu->sejour_id != $rpu->mutation_sejour_id && $sejour->fieldModified("mode_sortie", "mutation") &&
          !$sejour->UHCD
      ) {
        $sejour = $rpu->loadRefSejourMutation();
        $sejour->loadRefPatient();
        $sejour->loadLastLog();
        $sejour->_receiver = $receiver;
        $code = "A06";

        // On récupère l'affectation courante qui n'a pas été transmise (affectation suite à la mutation)
        $current_affectation          = $sejour->getCurrAffectation();
        $sejour->_ref_hl7_affectation = $current_affectation;
      }
      // Dans le cas d'une annulation d'hospitalisation
      elseif ($sejour->fieldModified("type", "urg") && $sejour->_cancel_hospitalization) {
        $sejour->loadRefPatient();
        $sejour->loadLastLog();
        $sejour->_receiver = $receiver;
        $code = "A07";

        // On récupère l'affectation courante qui n'a pas été transmise (affectation suite à la mutation)
        $current_affectation          = $sejour->getCurrAffectation();
        $sejour->_ref_hl7_affectation = $current_affectation;
      }
      // On est sur le séjour relicat, on ne synchronise aucun flux
      elseif ($rpu && $rpu->mutation_sejour_id && ($rpu->sejour_id != $rpu->mutation_sejour_id)) {
        return;
      }

      $code = $code ? $code : $this->getCodeSejour($sejour);

      // Dans le cas d'une création et que l'on renseigne entrée réelle et sortie réelle,
      // il est nécessaire de créer deux flux (A01 et A03)
      if ($sejour->_ref_last_log->type == "create" && $sejour->entree_reelle && $sejour->sortie_reelle) {
        $code = "A01";

        // Cas où :très souvent
        // * on est l'initiateur du message
        // * le destinataire ne supporte pas le message
        if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
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

        $patient = $sejour->_ref_patient;
        $patient->loadIPP($receiver->group_id);
        if (!$patient->_IPP) {
          if ($msg = $patient->generateIPP()) {
            CAppUI::setMsg($msg, UI_MSG_ERROR);
          }
        }

        // Cas où lors de l'entrée réelle j'ai une affectation qui n'a pas été envoyée
        if ($sejour->fieldModified("entree_reelle") && !$sejour->_old->entree_reelle) {
          $current_affectation = $sejour->getCurrAffectation();
        }

        $this->createMovement($code, $sejour, $current_affectation);

        // Envoi de l'événement
        $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour);

        $code = "A03";
      }

      if (!$code) {
        return;
      }

      // Cas où :
      // * on est l'initiateur du message
      // * le destinataire ne supporte pas le message
      if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
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

      $patient = $sejour->_ref_patient;
      $patient->loadIPP($receiver->group_id);
      if (!$patient->_IPP) {
        if ($msg = $patient->generateIPP()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
      }

      // Cas où lors de l'entrée réelle j'ai une affectation qui n'a pas été envoyée
      if ($sejour->fieldModified("entree_reelle") && !$sejour->_old->entree_reelle) {
        $current_affectation = $sejour->getCurrAffectation();
      }

      $this->createMovement($code, $sejour, $current_affectation);

      // Envoi de l'événement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour);
    }

    // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;
      $current_log = $affectation->_ref_current_log;

      if (!$current_log || $affectation->_no_synchro || !in_array($current_log->type, array("create", "store"))) {
        return;
      }

      // Affectation non liée à un séjour
      $sejour = $affectation->loadRefSejour();
      if (!$sejour->_id) {
        return;
      }

      // Première affectation des urgences on ne la transmet pas, seulement pour l'évènement de bascule
      // Sauf si nous sommes dans un séjour d'UHCD
      if ($affectation->_mutation_urg && !$sejour->UHCD) {
        return;
      }

      // Si on ne gère les séjours du bébé on ne transmet pas l'affectation si c'est un séjour enfant
      if (!$receiver->_configs["send_child_admit"]) {
        $naissance = new CNaissance();
        $naissance->sejour_enfant_id = $sejour->_id;
        $naissance->loadMatchingObject();
        if ($naissance->_id) {
          return;
        }
      }

      // Pas d'envoie d'affectation si la patient n'est pas sortie des urgences
      $rpu = new CRPU();
      $rpu->mutation_sejour_id = $sejour->_id;
      $rpu->loadMatchingObject();

      if ($rpu->_id) {
        $sejour_rpu = $rpu->loadRefSejour();
        if (!$affectation->_mutation_urg && $sejour_rpu->mode_sortie != "mutation") {
          return;
        }
      }

      // Pas d'envoie d'affectation pour les séjours reliquats
      // Sauf si le séjour est en UHCD
      $rpu = $sejour->loadRefRPU();
      if ($rpu && $rpu->mutation_sejour_id && ($rpu->sejour_id != $rpu->mutation_sejour_id) && !$sejour->UHCD) {
        return;
      }

      // Si le group_id du séjour est différent de celui du destinataire
      if ($sejour->group_id != $receiver->group_id) {
        return;
      }

      // On envoie pas les affectations prévisionnelles 
      if (!$receiver->_configs["send_provisional_affectation"] && $sejour->_etat == "preadmission") {
        return;
      }
      $first_affectation = $sejour->loadRefFirstAffectation();

      $code = $this->getCodeAffectation($affectation, $first_affectation);

      // Cas où : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }

      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;

      $patient = $sejour->_ref_patient;
      $patient->loadIPP($receiver->group_id);
      if (!$patient->_IPP) {
        if ($msg = $patient->generateIPP()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
      }

      $this->createMovement($code, $sejour, $affectation);
      $service = $affectation->loadRefService();
      $curr_affectation = $sejour->loadRefCurrAffectation();
      // On envoie pas de mouvement antérieur à la dernière affectation
      if (($service->uhcd || $service->radiologie || $service->urgence) && $affectation->sortie < $curr_affectation->sortie) {
        return;
      }

      // Ne pas envoyer la sortie si le séjour a une entrée réelle et si on modifie ou créé un affectation
      if (!$receiver->_configs["send_expected_discharge_with_affectation"] && $sejour->entree_reelle) {
        $sejour->sortie_prevue = null;
      }
   
      // Envoi de l'événement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $mbObject);
    }
    
    // Traitement Naissance
    if ($mbObject instanceof CNaissance) {
      $current_log = $mbObject->loadLastLog();
      if ($current_log->type != "create") {
        return;
      }

      $naissance = $mbObject;

      if (!$this->isMessageSupported($this->transaction, $this->message, "A28", $receiver)) {
        return;
      }

      $sejour_enfant = $naissance->loadRefSejourEnfant();

      // Création du bébé
      $enfant = $sejour_enfant->_ref_patient;
      $enfant->loadIPP($receiver->group_id);
      $enfant->_receiver  = $receiver;
      $enfant->_naissance_id = $naissance->_id;

      if (!$enfant->_IPP) {
        if ($msg = $enfant->generateIPP()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
      }

      // Envoi pas les patients qui n'ont pas d'IPP
      if (!$receiver->_configs["send_all_patients"] && !$enfant->_IPP) {
        return;
      }

      // Envoi du A28 pour la création du bébé
      $this->sendITI($this->profil, $this->transaction, $this->message, "A28", $enfant);

      $enfant->_IPP = null;

      // Si on gère les séjours du bébé on transmet le séjour !
      if ($receiver->_configs["send_child_admit"]) {
        $sejour_enfant->_receiver = $receiver;

        // Si le group_id du séjour est différent de celui du destinataire
        if ($sejour_enfant->group_id != $receiver->group_id) {
          return;
        }

        $code = $this->getCodeBirth($sejour_enfant);

        // Cas où :
        // * on est l'initiateur du message
        // * le destinataire ne supporte pas le message
        if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
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
        $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour_enfant);
      }
    }
  }

  /**
   * Create movement
   *
   * @param string       $code        HL7 event code
   * @param CSejour      $sejour      Admit
   * @param CAffectation $affectation Affectation
   *
   * @return CMovement|mixed
   */
  function createMovement($code, CSejour $sejour, CAffectation $affectation = null) {
    $insert = in_array($code, CHL7v2SegmentZBE::$actions["INSERT"]);
    $update = in_array($code, CHL7v2SegmentZBE::$actions["UPDATE"]);
    $cancel = in_array($code, CHL7v2SegmentZBE::$actions["CANCEL"]);

    $movement = new CMovement();
    // Initialise le mouvement 
    $movement->sejour_id = $sejour->_id;

    $receiver = $sejour->_receiver;
    $configs  = $receiver->_configs;

    $affectation_id = null;
    if ($affectation) {
      $current_log       = $affectation->_ref_current_log;
      $first_affectation = $sejour->loadRefFirstAffectation();
      /** @var CService $service */
      $service = $affectation->loadRefService();

      // Si le service est d'UHCD, de radiologie, d'urgence ou
      // Dans le cas où il s'agit de la première affectation du séjour et qu'on est en type "création" on ne recherche pas 
      // un mouvement avec l'affectation, mais on va prendre le mouvement d'admission
      if (($service->uhcd || $service->radiologie || $service->urgence) ||
          ($current_log && ($current_log->type == "create") && $first_affectation && ($first_affectation->_id == $affectation->_id))
      ) {
        switch ($configs["send_first_affectation"]) {
          case 'Z99':
            $affectation_id = $affectation->_id;
            $affectation    = null;
            break;

          default:
            $movement->affectation_id = $affectation->_id;
        }
      }
      else {
        $movement->affectation_id = $affectation->_id;  
      }
    }

    if ($insert) {
      // Dans le cas d'un insert le type correspond nécessairement au type actuel du séjour
      $movement->movement_type         = $sejour->getMovementType($code);
      $movement->original_trigger_code = $code;
      $movement->start_of_movement     = $this->getStartOfMovement($code, $sejour, $affectation);
      $movement->loadMatchingObject();
      $movement->store();

      return $sejour->_ref_hl7_movement = $movement;
    }
    elseif ($update) {
      // Dans le cas d'un update le type correspond à celui du trigger
      $movement_type = null;

      // Mise à jour entrée réelle
      if ($sejour->fieldModified("entree_reelle")) {
        $movement_type = "ADMI";
      }
      
      // Mise à jour sortie réelle
      if ($sejour->fieldModified("sortie_reelle")) {
        $movement_type = "SORT";
      }

      $movement->movement_type = $movement_type;

      // On ne recherche pas parmi les mouvements annulés
      $movement->cancel = 0;
    }

    $order = "affectation_id DESC";

    $movements = $movement->loadMatchingList($order);

    if (!empty($movements)) {
      $movement = reset($movements);
    }
    
    if ($update) {
      if ($movement->original_trigger_code == "A02") {
        if (!$affectation) {
          $affectation = new CAffectation();
        }
        $affectation->load($movement->affectation_id);
      }
      $movement->start_of_movement = $this->getStartOfMovement($movement->original_trigger_code, $sejour, $affectation, $movement);
    }
    
    // on annule un mouvement sauf dans le cas d'une annulation de mutation et que 
    if ($cancel && !($code == "A12" && $movement->original_trigger_code != "A02")) {
      $movement->cancel = 1;
    }

    if ($affectation_id) {
      $movement->affectation_id = $affectation_id;
    }

    $movement->store();
    
    return $sejour->_ref_hl7_movement = $movement;
  }

  /**
   * Get start of movement
   *
   * @param string       $code        HL7 event code
   * @param CSejour      $sejour      Admit
   * @param CAffectation $affectation Affectation
   * @param CMovement    $movement    Movement
   *
   * @return null|string
   */
  function getStartOfMovement($code, CSejour $sejour, CAffectation $affectation = null, CMovement $movement = null) {
    switch ($code) {
      // Admission hospitalisé / externe
      case 'A01':
      case 'A04':
        $sejour->_admit = true;

        // Date de l'admission
        return $sejour->entree_reelle;

      // Mutation : changement d'UF hébergement
      case 'A02':
        if (!$affectation) {
          return CMbDT::dateTime();
        }

        return $affectation->entree;

      // Changement de statut externe ou urgence vers hospitalisé
      case 'A06':
        // Changement de statut hospitalisé ou urgence vers externe
      case 'A07':
        // Changement de médecin responsable
      case 'A54':
        // Dans le cas d'une modification d'un mouvement, l'heure du mouvement est celle du mouvement initiateur
        if ($movement) {
          return $movement->start_of_movement;
        }

        // Date du transfert
        return CMbDT::dateTime();

      // Absence provisoire (permission) et mouvement de transfert vers un plateau technique pour acte (<48h)
      case 'A21':
        // Changement d'UF médicale
      case 'Z80':
        // Changement d'UF de soins
      case 'Z84':
        if (!$affectation) {
          return CMbDT::dateTime();
        }

        return $affectation->entree;

      // Retour d'absence provisoire (permission) et mouvement de transfert vers un plateau technique pour acte (<48h)
      case 'A22':
        if (!$affectation) {
          return CMbDT::dateTime();
        }

        return $affectation->sortie;

        // Sortie définitive
      case 'A03':
        // Date de la sortie
        return $sejour->sortie_reelle;

      // Pré-admission
      case 'A05':
      case 'A14':
        // Date de la pré-admission
        return $sejour->entree_prevue;

      // Sortie en attente
      case 'A16':
        // Date de la sortie
        return $sejour->sortie;

      default:
    }
  }

  /**
   * Get bascule HL7 event code
   *
   * @param CSejour $from Admit from
   * @param CSejour $to   Admit to
   *
   * @return string
   */
  function getBasculeCode(CSejour $from, CSejour $to) {
    $matrix = array(    // comp/M   comp/C   comp/O   bebe/*   ambu/*   urg/*   seances/* exte/*
      "comp/M"    => array( null,   "Z99",   "Z99",   "Z99",   "Z99",   "A07",   "Z99",   "A07"),
      "comp/C"    => array("Z99",    null,   "Z99",   "Z99",   "Z99",   "A07",   "Z99",   "A07"),
      "comp/O"    => array("Z99",   "Z99",    null,   "Z99",   "Z99",   "A07",   "Z99",   "A07"),
      "bebe/*"    => array("A06",   "A06",   "A06",    null,   "A06",   "A07",   "A06",   "A07"),
      "ambu/*"    => array("Z99",   "Z99",   "Z99",   "Z99",    null,   "A07",   "Z99",   "A07"),
      "urg/*"     => array("A06",   "A06",   "A06",   "A06",   "A06",    null,   "A06",   "Z99"),
      "seances/*" => array("Z99",   "Z99",   "Z99",   "Z99",   "Z99",   "A07",    null,   "A07"),
      "exte/*"    => array("A06",   "A06",   "A06",   "A06",   "A06",   "Z99",   "A06",    null),
    );
    
    $from->completeField("type", "type_pec");
    $type_from     = $from->type;
    $type_pec_from = $from->type_pec;
    
    $to->completeField("type", "type_pec");
    $type_to     = $to->type;
    $type_pec_to = $to->type_pec;
    
    // psy || ssr == seances
    if (in_array($type_from, array("psy", "ssr"))) {
      $type_from = "seances";
    }
    if (in_array($type_to, array("psy", "ssr"))) {
      $type_to = "seances";
    }
    
    /* // TODO prendre en compte les sejours de type nouveau né
    $naissances = $from->loadRefsNaissances();
    foreach ($naissances as $_naissance) {
      if ($naissances->sejour_bebe_id == $from->_id) {
        $type_from = "bebe";
        break;
      }
    }*/
    
    $row = CMbArray::first($matrix, array("$type_from/$type_pec_from", "$type_from/*"));

    if (!$row) {
      return $this->getModificationAdmitCode($from->_receiver);
    }
    
    $columns = array_flip(array_keys($matrix));
    $col_num = CMbArray::first($columns, array("$type_to/$type_pec_to", "$type_to/*"));

    if ($columns === null) {
      return $this->getModificationAdmitCode($from->_receiver);
    }

    if ($row[$col_num] == "Z99") {
      return $this->getModificationAdmitCode($from->_receiver);
    }

    return $row[$col_num];
  }

  /**
   * Get admit HL7 event code
   *
   * @param CSejour $sejour Admit
   *
   * @return null|string
   */
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
        // Pending admit
        if ($configs["iti31_pending_event_management"] && $sejour->recuse == -1) {
          return "A14";
        }

        return "A05";
      }

      // Cancel the pending admission
      if ($configs["iti31_pending_event_management"] && $sejour->recuse == -1 && $sejour->fieldModified("annule", "1")) {
        return "A27";
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

      // Réattribution dossier administratif
      if ($sejour->fieldModified("patient_id")) {
        return "A44";
      }

      if (!$configs["modification_before_admit"] && !$sejour->entree_reelle) {
        return;
      }

      // Simple modification ?
      return $this->getModificationAdmitCode($sejour->_receiver);
    }

    // Cas d'un séjour en cours (entrée réelle)
    if ($sejour->_etat == "encours") {
      // Admission faite
      $sejour_old = $sejour->_old;
      if ($sejour->fieldModified("entree_reelle") && !$sejour_old->entree_reelle
          || $sejour->entree_reelle && !$sejour_old->entree_reelle
      ) {
        // Patient externe
        if (in_array($sejour->type, self::$outpatient)) {
          return "A04";
        }

        // Admission hospitalisé
        return "A01";
      }
      
      // Confirmation de sortie
      if ($sejour->fieldFirstModified("confirme")) {
        return "A16";
      }

      // Annulation confirmation de sortie
      if ($sejour->fieldEmptyValued("confirme")) {
        return "A25";
      }

      // Bascule du type et type_pec
      if ($sejour->fieldModified("type")) {
        $sejour->_old->_receiver = $sejour->_receiver;

        return $this->getBasculeCode($sejour->_old, $sejour);
      }

      // Changement du médecin responsable
      if ($sejour->fieldModified("praticien_id")) {
        $first_log = $sejour->loadFirstLog();

        $praticien_id = $sejour->getValueAtDate($first_log->date, "praticien_id");

        $send_change_attending_doctor = $configs["send_change_attending_doctor"];
        // Annulation du médecin responsable
        if ($sejour->praticien_id == $praticien_id) {
          return (($send_change_attending_doctor == "A54") ? "A55" : $this->getModificationAdmitCode($receiver));
        }

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

      // Cas d'une rétablissement on simule une nouvelle admission
      if ($sejour->fieldModified("annule", "0")) {
        // Patient externe
        if (in_array($sejour->type, self::$outpatient)) {
          return "A04";
        }

        // Admission hospitalisé
        return "A01";
      }
      
      // Annulation de la sortie réelle
      if ($sejour->_old->sortie_reelle && !$sejour->sortie_reelle) {
        return "A13";
      }

      // Notification sur le transfert
      if ($configs["iti31_pending_event_management"]
          && $sejour->fieldModified("mode_sortie")
          && $sejour->mode_sortie == "transfert"
      ) {
        return "A15";
      }

      // Annulation de la notification sur le transfert
      if ($configs["iti31_pending_event_management"]
          && $sejour->_old->mode_sortie
          && $sejour->_old->mode_sortie == "transfert"
          && !$sejour->mode_sortie
      ) {
        return "A26";
      }

      // On ne transmet pas les modifications d'un séjour dès lors que celui-ci a une entrée réelle
      if (!$receiver->_configs["send_change_after_admit"]) {
        return null;
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

      // On ne transmet pas les modifications d'un séjour dès lors que celui-ci a une entrée réelle
      if (!$receiver->_configs["send_change_after_admit"]) {
        return null;
      }

      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }

    return null;
  }

  /**
   * Get affectation HL7 event code
   *
   * @param CAffectation $affectation       Affectation
   * @param CAffectation $first_affectation First affectation
   *
   * @return null|string
   */
  function getCodeAffectation(CAffectation $affectation, CAffectation $first_affectation = null) {
    $current_log = $affectation->_ref_current_log;
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $receiver = $affectation->_receiver;
    $configs  = $receiver->_configs;
    $service  = $affectation->loadRefService();

    $code = "A02";
    if ($service->uhcd || $service->radiologie || $service->urgence) {
      $code = $this->getModificationAdmitCode($receiver);
    }

    if ($current_log->type == "create") {
      /* Affectation dans un service externe */
      if ($service->externe) {
        return "A21";
      }

      // Dans le cas où il s'agit de la première affectation du séjour on ne fait pas une mutation mais une modification
      if ($first_affectation && ($first_affectation->_id == $affectation->_id)) {
        switch ($configs["send_first_affectation"]) {
          case 'Z99':
            return $this->getModificationAdmitCode($receiver);
          default:
            return $code;
        }
      }
      
      // Création d'une affectation
      switch ($configs["send_transfer_patient"]) {
        case 'Z99':
          return $this->getModificationAdmitCode($receiver);
        default:
          return $code;
      }
    }
            
    /* Affectation dans un service externe effectuée */
    if ($service->externe && !$affectation->_old->effectue && $affectation->effectue) {
      return "A22";
    }

    /* Affectation dans un service externe effectuée */
    if ($service->externe && $affectation->_old->effectue && !$affectation->effectue) {
      return "A53";
    }
    
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

  /**
   * Get birth HL7 event code
   *
   * @param CSejour $sejour Admit
   *
   * @return null|string
   */
  function getCodeBirth(CSejour $sejour) {
    // Cas d'une pré-admission
    if ($sejour->_etat == "preadmission") {
      return "A05";
    }

    // Patient externe
    if (in_array($sejour->type, self::$outpatient)) {
      return "A04";
    }

    // Admission hospitalisé
    return "A01";
  }

  /**
   * Get affectation HL7 event code
   *
   * @param CReceiverHL7v2 $receiver Receiver HL7v2
   *
   * @return string
   */
  function getModificationAdmitCode(CReceiverHL7v2 $receiver) {
    mbLog($receiver);
    switch ($receiver->_i18n_code) {
      // Cas de l'extension française : Z99
      case "FR":
        $code = "Z99";
        break;
      // Cas internationnal : A08
      default:
        $code = $receiver->_configs["modification_admit_code"];
    }
    
    return $code;
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if ($mbObject instanceof CSejour) {
      /** @var CSejour $sejour */
      $sejour = $mbObject;
      /** @var CInteropReceiver $receiver */
      $receiver = $sejour->_receiver;
      $receiver->getInternationalizationCode($this->transaction);

      foreach ($sejour->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }

        $sejour1_nda = $sejour->_NDA = $infos_fus["sejour1_nda"];

        /** @var CSejour $sejour_elimine */
        $sejour_elimine = $infos_fus["sejourElimine"];
        $sejour2_nda    = $sejour_elimine->_NDA = $infos_fus["sejour2_nda"];
        $receiver->loadConfigValues();

        // Cas 2 NDA : Suppression du deuxième séjour
        if ($sejour1_nda && $sejour2_nda) {
          if ($receiver->_configs["send_a42_onmerge"]) {
            continue;
          }
          // Dans la pré-admission : A38
          if ($sejour_elimine->_etat == "preadmission") {
            $code = "A38";
          }

          // En admission / clôturé : A11
          else {
            $code = "A11";
          }

          $sejour_elimine->_receiver = $receiver;
          $sejour_elimine->loadRefPatient();

          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }

          $this->createMovement($code, $sejour_elimine);

          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour_elimine);

          continue;
        }
      }
    }
  }

  /**
   * Trigger when merge failed
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onMergeFailure(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    // On va réatribuer les idexs en cas de problème dans la fusion
    foreach ($mbObject->_fusion as $group_id => $infos_fus) {
      if (!$infos_fus || !array_key_exists("idexs_changed", $infos_fus)) {
        return false;
      }

      foreach ($infos_fus["idexs_changed"] as $idex_id => $tag_name) {
        $idex = new CIdSante400();
        $idex->load($idex_id);

        if (!$idex->_id) {
          continue;
        }

        // Réattribution sur l'objet non supprimé
        $sejour_elimine  = $infos_fus["sejourElimine"];
        $idex->object_id = $sejour_elimine->_id;

        $idex->tag = $tag_name;
        $idex->last_update = CMbDT::dateTime();
        $idex->store();
      }
    }
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      /** @var CInteropReceiver $receiver */
      $receiver = $sejour->_receiver;
      $receiver->getInternationalizationCode($this->transaction);

      foreach ($sejour->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }

        $sejour1_nda = $sejour->_NDA = $infos_fus["sejour1_nda"];

        /** @var CSejour $sejour_elimine */
        $sejour_elimine = $infos_fus["sejourElimine"];
        $sejour2_nda    = $sejour_elimine->_NDA = $infos_fus["sejour2_nda"];

        // Suppression de tous les mouvements du séjours à éliminer
        $movements = $sejour_elimine->loadRefsMovements();
        foreach ($movements as $_movement) {
          $_movement->last_update = "now";
          $_movement->cancel      = 1;
          $_movement->store();
        }

        // Cas 0 NDA : Aucune notification envoyée
        if (!$sejour1_nda && !$sejour2_nda) {
          continue;
        }

        // Cas 1 NDA : Pas de message de fusion mais d'une modification de séjour
        if ($sejour1_nda xor $sejour2_nda) {
          if ($sejour2_nda) {
            $sejour->_NDA = $sejour2_nda;
          }

          $code = $this->getModificationAdmitCode($receiver);
          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }

          $this->createMovement($code, $sejour);

          $sejour->loadRefPatient();

          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour);
          continue;
        }

        $receiver->loadConfigValues();
        //Cas 2 NDA : message de fusion si la configuration est activé
        if ($receiver->_configs["send_a42_onmerge"] && $sejour1_nda && $sejour2_nda) {
          $code = "A42";
          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }
          $sejour->loadRefPatient();
          $sejour->_sejour_elimine = $sejour_elimine;
          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour);
          continue;
        }
      }
    }
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);  

    // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;

      if ($affectation->_no_synchro) {
        return;
      }
      
      $sejour = $affectation->loadRefSejour();
      if (!$sejour->_id || $sejour->_etat == "preadmission") {
        return;
      }

      // Si le group_id du séjour est différent de celui du destinataire
      if ($sejour->group_id != $receiver->group_id) {
        return;
      }

      /* Annulation de l'affectation dans un service externe */
      $service = $affectation->loadRefService();
      if ($service->externe) {
        // Affectation effectuée 
        if ($affectation->effectue) {
          $code = "A53";
        }
        else {
          $code = "A52";
        }
      }
      else {
        // Annulation (suppression) d'une affectation
        $code = "A12";
      }
            
      // Cas où : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }
            
      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;

      $this->createMovement($code, $sejour, $affectation);
   
      // Envoi de l'événement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $mbObject);
    }  
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
  }
}