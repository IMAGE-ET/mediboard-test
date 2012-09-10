<?php /* $Id: CHPrimXMLEvenementsServeurActes.class.php 15933 2012-06-19 10:43:16Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 15933 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsServeurIntervention extends CHPrimXMLEvenementsServeurActivitePmsi {
  var $actions = array(
    'création'     => "création",
    'remplacement' => "remplacement",
    'modification' => "modification",
    'suppression'  => "suppression",
    'information'  => "information",
  );
  
  function __construct() {
    $this->sous_type = "evenementServeurIntervention";
    $this->evenement = "evt_serveurintervention";
    
    parent::__construct("serveurActes", "msgEvenementsServeurActes");
  }

  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsServeurActes");
  }
  
  function generateFromOperation(COperation $operation) {
    $evenementsServeurActes = $this->documentElement;

    $evenementServeurIntervention = $this->addElement($evenementsServeurActes, "evenementServeurIntervention");
    $actionConversion = array (
      "create" => "création",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = (!$operation->loadLastLog()) ? "modification" : $actionConversion[$operation->_ref_last_log->type];
    if ($operation->fieldModified("annulee", 1)) {
      $action = "suppression";
    }
    $this->addAttribute($evenementServeurIntervention, "action", $action);
    
    // Date de l'action
    $this->addDateTimeElement($evenementServeurIntervention, "dateAction");

    // Ajout du patient
    $patient = $this->addElement($evenementServeurIntervention, "patient");
    $mbPatient = $operation->_ref_sejour->_ref_patient;
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue
    $venue = $this->addElement($evenementServeurIntervention, "venue");
    $mbSejour = $operation->_ref_sejour;
    $this->addVenue($venue, $mbSejour, null, true);

    // Ajout de l'intervention
    $operation->loadLastId400($this->_ref_receiver->_tag_hprimxml);
    $intervention = $this->addElement($evenementServeurIntervention, "intervention");
    $this->addIntervention($intervention, $operation);
      
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $data = array();
    $xpath = new CHPrimXPath($this);   
    
    $evenementsServeurActes       = $xpath->queryUniqueNode("/hprim:evenementsServeurActes");
    $evenementServeurIntervention = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:evenementServeurIntervention");
    
    $data['action']               = $this->getActionEvenement("hprim:evenementServeurIntervention ", $evenementsServeurActes);
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurIntervention);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementServeurIntervention);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    $data['intervention']         = $xpath->queryUniqueNode("hprim:intervention", $evenementServeurIntervention);
    $data['idSourceIntervention'] = $this->getIdSource($data['intervention'], false);
    $data['idCibleIntervention']  = $this->getIdCible($data['intervention'], false);
        
    return $data; 
  }
  
   /**
   * Enregistrement des interventions
   * 
   * @param CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq    DOM Acquittement 
   * @param CMbObject                                 $mbObject   Object
   * @param array                                     $data       Data that contain the nodes 
   * 
   * @return string Acquittement 
   **/
  function handle(CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq, CMbObject $mbObject, $data) {
    $operation      = $mbObject;
    $exchange_hprim = $this->_ref_echange_hprim;
    $sender         = $exchange_hprim->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;
    
    $warning = null;
    $comment = null;
    
    // Acquittement d'erreur : identifiants source du patient / séjour non fournis
    if (!$data['idSourcePatient'] || !$data['idSourceVenue']) {
      return $exchange_hprim->setAckError($dom_acq, "E206", null, $mbObject);
    }
    
    // IPP non connu => message d'erreur
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $data['idSourcePatient']);
    if (!$IPP->_id) {
      return $exchange_hprim->setAckError($dom_acq, "E013", null, $mbObject);   
    }
    
    // Chargement du patient
    $patient = new CPatient();   
    $patient->load($IPP->object_id);
    
    // Num dossier non connu => message d'erreur
    $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $data['idSourceVenue']);
    if (!$NDA->_id) {
      return $exchange_hprim->setAckError($dom_acq, "E014", null, $mbObject);
    }
    
    // Chargement du séjour
    $sejour = new CSejour();
    $sejour->load($NDA->object_id);
    
    // Si patient H'XML est différent du séjour
    if ($sejour->patient_id != $patient->_id) {
      return $exchange_hprim->setAckError($dom_acq, "E015", null, $mbObject);
    }

    // Chargement du patient du séjour
    $sejour->loadRefPatient();
    $operation->sejour_id = $sejour->_id;
    
    // Mapping du séjour
    $sejour = $this->mappingVenue($data['venue'], $sejour);
    
    // Notifier les autres destinataires autre que le sender
    $sejour->_eai_initiateur_group_id = $sender->group_id;
    if (!$msgVenue = $sejour->store()) {
      return $exchange_hprim->setAck($dom_acq, "A102", $msgVenue, null, $sejour);
    }
    
    // idex de l'intervention
    $idex = CIdSante400::getMatch("COperation", $sender->_tag_hprimxml, $data['idSourceIntervention']);
    
    if ($idex->_id) {
      $operation_source = new COperation();
      $operation_source->load($idex->object_id);

      if ($operation_source->sejour_id != $sejour->_id) {
        return $exchange_hprim->setAckError($dom_acq, "E204", null, $mbObject);
      } 

      $operation = $operation_source;     
    }
    
    if (!$operation->_id) {
      // Recherche de la salle
      $salle = $this->getSalle($data['intervention']);
      if ($salle->nom && !$salle->_id) {
        $comment = "Salle '$salle->nom' inconnue dans l'infrastructure de l'établissement";
        return $exchange_hprim->setAckError($dom_acq, "E202", $comment, $mbObject);
      }
      $operation->salle_id = $salle->_id;
      
      // Mapping du chirurgien
      $mediuser = $this->getParticipant($data['intervention']);
      if (($mediuser->adeli && !$mediuser->_id) || !$mediuser->adeli) {
        $comment = $mediuser->adeli ? "Participant '$mediuser->adeli' inconnu" : "Le code ADELI n'est pas renseigné";
        return $exchange_hprim->setAckError($dom_acq, "E203", $comment, $mbObject);
      }
      $operation->chir_id = $mediuser->_id;
      
      // Mapping de la plage
      $plageOp = $this->mappingPlage($data['intervention'], $operation);
   
      // Recherche d'une intervention existante sinon création  
      $operation->loadMatchingObject();
    }

    // Mapping de l'intervention
    $this->mappingIntervention(($data['intervention']), $operation);
    
    // Store de l'intervention
    // Notifier les autres destinataires autre que le sender
    $operation->_eai_initiateur_group_id = $sender->group_id;
    $msgInterv = $operation->store();
    
    CEAIMbObject::storeIdex($idex, $operation, $sender);
    $modified_fields = CEAIMbObject::getModifiedFields($operation);
          
    $codes = array ($msgInterv ? "A201" : "I201");
    if ($msgInterv) {
      $warning .= $msgInterv." ";
    } 
    else {
      $comment .= "Intervention : $operation->_id.";
      $comment .= $modified_fields ? " Les champs mis à jour sont les suivants : $modified_fields." : null;
    }
    
    return $exchange_hprim->setAck($dom_acq, $codes, $warning, $comment, $operation);
  }
}
?>