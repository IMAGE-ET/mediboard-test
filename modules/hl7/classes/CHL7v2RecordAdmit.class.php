<?php /* $Id:$ */

/**
 * Record admit, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordAdmit 
 * Record admit, message XML HL7
 */

class CHL7v2RecordAdmit extends CHL7v2MessageXML {
  function getContentNodes() {
    $data  = parent::getContentNodes();
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
    
    $this->queryNodes("NK1", null, $data, true);
    
    $this->queryNodes("ROL", null, $data, true);    
    
    $PV1 = $this->queryNode("PV1", null, $data, true);

    $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);

    $this->queryNode("PV2", null, $data, true);
    
    $this->queryNode("ZBE", null, $data, true);
    
    $this->queryNode("ZFP", null, $data, true);
    
    $this->queryNode("ZFV", null, $data, true);
    
    $this->queryNode("ZFM", null, $data, true);
    
    $this->queryNode("ZFD", null, $data, true);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    $event_temp = $ack->event;
    
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
        
    // Traitement du patient
    $hl7v2_record_person = new CHL7v2RecordPerson();
    $hl7v2_record_person->_ref_exchange_ihe = $exchange_ihe;
    $msg_ack = $hl7v2_record_person->handle($ack, $newPatient, $data);
    
    // Retour de l'acquittement si erreur sur le traitement du patient
    if ($exchange_ihe->statut_acquittement == "AR") {
      return $msg_ack;
    }
        
    // Traitement du séjour
    $ack = new CHL7v2Acknowledgment($event_temp);
    $ack->message_control_id = $data['identifiantMessage'];
    $ack->_ref_exchange_ihe  = $exchange_ihe;
   
    $newVenue = new CSejour();
    
    $function_handle = "handle$exchange_ihe->code";
    if (!method_exists($this, $function_handle)) {
      return $exchange_ihe->setAckAR($ack, "E006", null, $newVenue);
    }
    
    $this->$function_handle($ack, $newVenue, $data);
  } 
  
  function handleA01(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA02(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA03(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA04(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA05(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA06(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA07(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA11(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA12(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA13(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA28(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA31(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA38(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA40(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA44(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA45(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA54(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleA55(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
  
  function handleZ99(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    
  }
}

?>