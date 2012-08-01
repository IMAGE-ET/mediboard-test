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
    
    // Ajout de l'intervention ou consultation ou sejour
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
   * @param CHPrimXMLAcquittementsServeurActivitePmsi $ack        DOM Acquittement 
   * @param CMbObject                                 $mbObject   Object
   * @param array                                     $data       Data that contain the nodes 
   * 
   * @return string Acquittement 
   **/
  function handle(CHPrimXMLAcquittementsServeurActivitePmsi $ack, CMbObject $mbObject, $data) {
    $exchange_hprim = $this->_ref_echange_hprim;
    $sender         = $exchange_hprim->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;
    
    // Acquittement d'erreur : identifiants source du patient / séjour non fournis
    if (!$data['idSourcePatient'] || !$data['idSourceVenue']) {
      return $exchange_hprim->setAckErr($ack, "E206", null, $mbObject);
    }
    
    // IPP non connu => message d'erreur
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $data['idSourcePatient']);
    if (!$IPP->_id) {
      return $exchange_hprim->setAckErr($ack, "E013", null, $mbObject);   
    }
    
    // Chargement du patient
    $patient = new CPatient();   
    $patient->load($IPP->object_id);
    
    // Num dossier non connu => message d'erreur
    $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $data['idSourceVenue']);
    if (!$NDA->_id) {
      return $exchange_hprim->setAckErr($ack, "E014", null, $mbObject);
    }
    
    // Chargement du séjour
    $sejour = new CSejour();
    $sejour->load($NDA->object_id);
    
    // Si patient H'XML est différent du séjour
    if ($sejour->patient_id != $patient->_id) {
      return $exchange_hprim->setAckErr($ack, "E015", null, $mbObject);
    }

    // Chargement du patient du séjour
    $sejour->loadRefPatient();
    
    // Traitement de la date/heure début, et durée de l'opération
    
    $date_op  = mbDate($date_debut);
    $time_op  = mbTime($date_debut);
    $temps_op = mbSubTime(mbTime($date_debut), mbTime($date_fin)); 
    
    // Recherche de la salle
    $salle      = new CSalle();
    $salle->nom = $nom_salle;
    if (!$salle->loadMatchingObject()) {
      return $exchange_hprim->setAckErr($ack, "E014", null, $mbObject);
      
      CAppUI::stepAjax("La salle '$nom_salle' n'a pas été retrouvée dans Mediboard", UI_MSG_WARNING);
      $results["count_erreur"]++;
      continue;
    }
    
    // Recherche d'une éventuelle PlageOp
    $plageOp           = new CPlageOp();
    $plageOp->chir_id  = $mediuser->_id;
    $plageOp->salle_id = $salle->_id;
    $plageOp->date     = $date_op;
    foreach ($plageOp->loadMatchingList() as $_plage) {
      // Si notre intervention est dans la plage Mediboard
      if ($_plage->debut <= $time_op && $temps_op <= $_plage->fin) {
        $plageOp = $_plage;
        
        break;
      }
    }

    // Recherche d'une intervension existante sinon création
    $operation                 = new COperation();
    $operation->sejour_id      = $sejour->_id;
    $operation->chir_id        = $mediuser->_id;
    $operation->plageop_id     = $plageOp->_id;
    $operation->salle_id       = $salle->_id;
    if (!$operation->plageop_id) {
      $operation->date         = $date_op;
    }
    $operation->temp_operation = $temps_op;
    $operation->time_operation = $time_op;
    $operation->loadMatchingObject();
    
    $operation->libelle        = $libelle;
    $operation->cote           = $cote ? $cote : "inconnu";
    
    if ($msg = $operation->store()) {
      CAppUI::stepAjax($msg, UI_MSG_WARNING);
      $results["count_erreur"]++;
      continue;
    }
  }
}
?>