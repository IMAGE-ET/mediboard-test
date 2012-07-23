<?php /* $Id: CHPrimXMLEvenementsServeurActes.class.php 15933 2012-06-19 10:43:16Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 15933 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsServeurIntervention extends CHPrimXMLEvenementsServeurActivitePmsi {
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
    
    $evenementServeurActe = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:evenementServeurActe");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementServeurActe);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    $data['intervention']         = $xpath->queryUniqueNode("hprim:intervention", $evenementServeurActe);
    $data['idSourceIntervention'] = $this->getIdSource($data['intervention'], false);
    $data['idCibleIntervention']  = $this->getIdCible($data['intervention'], false);

    
    return $data; 
  }
  
  
}
?>