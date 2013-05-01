<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEnregistrementPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'création' => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );
  
  function __construct() {   
    $this->sous_type = "enregistrementPatient";
             
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");

    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = (!$mbPatient->_ref_last_log) ? "modification" : $actionConversion[$mbPatient->_ref_last_log->type];

    $this->addAttribute($enregistrementPatient, "action", $action);
    
    $patient = $this->addElement($enregistrementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbPatient, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:enregistrementPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  /**
   * Recording a patient with an IPP in the system
   *
   * @param CHPrimXMLAcquittementsPatients $dom_acq
   * @param CEchangeHprim $echg_hprim
   * @param CPatient $newPatient
   * @param array $data
   *
   * @return CHPrimXMLAcquittementsPatients $msgAcq 
   **/
  function enregistrementPatient($dom_acq, &$newPatient, $data) {
    // Traitement du message des erreurs
    $commentaire = $avertissement = $msgID400 = $msgIPP = "";
    $_IPP_create   = $_modif_patient = false;
    
    $echg_hprim = $this->_ref_echange_hprim; 
    $echg_hprim->_ref_sender->loadConfigValues();
    $sender     = $echg_hprim->_ref_sender;
    
    $idSourcePatient = $data['idSourcePatient'];
    $idCiblePatient  = $data['idCiblePatient'];
    
    // Acquittement d'erreur : identifiants source et cible non fournis
    if (!$idCiblePatient && !$idSourcePatient) {
      return $echg_hprim->setAckError($dom_acq, "E005", $commentaire, $newPatient);
    }    
    
    // Si CIP
    if (!CAppUI::conf('sip server')) {
      $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $idSourcePatient);
     
      // idSource non connu
      if (!$IPP->_id) {
        // idCible fourni
        if ($idCiblePatient) {
          if ($newPatient->load($idCiblePatient)) {
            // Le patient trouvé est-il différent ?
            /*if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
              $commentaire = "Le nom et/ou le prénom sont très différents."; 
              $msgAcq = $dom_acq->generateAcquittements("erreur", "E016", $commentaire);
              $doc_valid = $dom_acq->schemaValidate();
              $echg_hprim->setObjectIdClass("CPatient", $newPatient->_id);
              $echg_hprim->setAckError($doc_valid, $msgAcq, "erreur");
              return $msgAcq;
            }*/
        
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          
            // On store le patient
            $msgPatient = CEAIPatient::storePatient($newPatient, $sender);

            $modified_fields = CEAIPatient::getModifiedFields($newPatient);
            
            $_code_IPP      = "I021";
            $_modif_patient = true; 
            $commentaire    = "Patient modifié : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
          }
          else {
            $_code_IPP = "I020";
          }
        }
        else {
          $_code_IPP = "I022";  
        }
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
              
        if (!$newPatient->_id) {
          // Patient retrouvé
          if ($newPatient->loadMatchingPatient()) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
            
            // On store le patient
            $msgPatient = CEAIPatient::storePatient($newPatient, $sender);
        
            $modified_fields = CEAIPatient::getModifiedFields($newPatient);
            
            $_code_IPP      = "A021";
            $_modif_patient = true; 
            $commentaire    = "Patient modifié : $newPatient->_id.  Les champs mis à jour sont les suivants : $modified_fields.";           
          }
          else {
            // On store le patient
            $msgPatient = CEAIPatient::storePatient($newPatient, $sender);
          
            $commentaire = "Patient créé : $newPatient->_id. ";
          }
        }

        $msgIPP = CEAIPatient::storeIPP($IPP, $newPatient, $sender);
        
        $codes = array ($msgPatient ? ($_modif_patient ? "A003" : "A002") : ($_modif_patient ? "I002" : "I001"), $msgIPP ? "A005" : $_code_IPP);
        
        if ($msgPatient || $msgIPP) {
          $avertissement = $msgPatient." ".$msgIPP;
        }
        else {
          $commentaire .= "IPP créé : $IPP->id400.";
        }
      } 
      // idSource connu
      else {
        $newPatient->load($IPP->object_id);
        /*if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
          $commentaire = "Le nom et/ou le prénom sont très différents."; 
          $msgAcq = $dom_acq->generateAcquittements("erreur", "E016", $commentaire);
          $doc_valid = $dom_acq->schemaValidate();
          $echg_hprim->setObjectIdClass("CPatient", $newPatient->_id);
          $echg_hprim->setAckError($doc_valid, $msgAcq, "erreur");
          return $msgAcq;
        }*/
            
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$idCiblePatient) {
          $_code_IPP = "I023"; 
        }
        else {
          $tmpPatient = new CPatient();
          // idCible connu
          if ($tmpPatient->load($idCiblePatient)) {
            if ($tmpPatient->_id != $IPP->object_id) {
              $commentaire = "L'identifiant source fait référence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
              return $echg_hprim->setAckError($dom_acq, "E004", $commentaire, $newPatient);
            }
            $_code_IPP = "I024"; 
          }
          // idCible non connu
          else {
            $_code_IPP = "A020";
          }
        }
        
        // On store le patient
        $msgPatient = CEAIPatient::storePatient($newPatient, $sender);
        
        $modified_fields = CEAIPatient::getModifiedFields($newPatient);
        
        $codes = array ($msgPatient ? "A003" : "I002", $_code_IPP);
        
        if ($msgPatient) {
          $avertissement = $msgPatient." ";
        }
        else {
          $commentaire = "Patient modifié : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP associé : $IPP->id400.";
        }
      }
      
      return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
    }    
  }
}