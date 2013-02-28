<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2ChangePatientIdentifierList 
 * Change patient identifier list, message XML HL7
 */
class CHL7v2ChangePatientIdentifierList extends CHL7v2MessageXML {
  static $event_codes = "A46 A47";

  /**
   * Get contents
   *
   * @return array
   */
  function getContentNodes() {
    $data = parent::getContentNodes();

    $this->queryNode("MRG", null, $data, true);
       
    return $data;
  }

  /**
   * Handle change patient identifier list message
   *
   * @param CHL7Acknowledgment $ack     Acknowledgment
   * @param CPatient           $patient Person
   * @param array              $data    Data
   *
   * @return string|void
   */
  function handle(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$data['personIdentifiers']) {
      return $exchange_ihe->setAckAR($ack, "E100", null, $patient);
    }
 
    $function_handle = "handle$exchange_ihe->code";
    
    if (!method_exists($this, $function_handle)) {
      return $exchange_ihe->setAckAR($ack, "E006", null, $patient);
    }
    
    return $this->$function_handle($ack, $patient, $data);
  }

  /**
   * Handle event A46
   *
   * @param CHL7Acknowledgment $ack     Acknowledgment
   * @param CPatient           $patient Person
   * @param array              $data    Data
   *
   * @return string
   */
  function handleA46(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    $handle_mode = CHL7v2Message::$handle_mode;
    
    CHL7v2Message::$handle_mode = "simple";
    
    $msg = $this->handleA47($ack, $patient, $data);
    
    CHL7v2Message::$handle_mode = $handle_mode;
    
    return $msg;
  }

  /**
   * Handle event A47
   *
   * @param CHL7Acknowledgment $ack     Acknowledgment
   * @param CPatient           $patient Person
   * @param array              $data    Data
   *
   * @return string
   */
  function handleA47(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
   
    $this->_ref_sender = $sender;
    
    $incorrect_identifier = null;
    
    if (CHL7v2Message::$handle_mode == "simple") {
      $MRG_4 = $this->queryNodes("MRG.4", $data["MRG"])->item(0);
      
      $incorrect_identifier = $this->queryTextNode("CX.1", $MRG_4);
    }
    else {
      $MRG_1 = $this->queryNodes("MRG.1", $data["MRG"])->item(0);
      
      if ($this->queryTextNode("CX.5", $MRG_1) == "PI") {
        $incorrect_identifier = $this->queryTextNode("CX.1", $MRG_1);
      }
    }
    
    // Chargement de l'IPP   
    $IPP_incorrect = new CIdSante400();
    if ($incorrect_identifier) {
      $IPP_incorrect = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $incorrect_identifier);
    }
    
    // PI non connu (non fourni ou non retrouvé)
    if (!$incorrect_identifier || !$IPP_incorrect->_id) {
      return $exchange_ihe->setAckAR($ack, "E141", null, $patient);
    }

    $patient->load($IPP_incorrect->object_id);

    // Passage en trash de l'IPP du patient a éliminer
    if ($msg = $patient->trashIPP($IPP_incorrect)) {
      return $exchange_ihe->setAckAR($ack, "E140", $msg, $patient);
    }  
    
    // Sauvegarde du nouvel IPP
    $IPP = new CIdSante400();
    $IPP->object_id    = $patient->_id;
    $IPP->object_class = "CPatient";
    $IPP->id400        = $data['personIdentifiers']["PI"];
    $IPP->tag          = $sender->_tag_patient;
    $IPP->last_update  = mbDateTime();
    
    if ($msg = $IPP->store()) {
      return $exchange_ihe->setAckAR($ack, "E140", $msg, $patient);
    }  
    
    return $exchange_ihe->setAckAA($ack, "I140", null, $patient);
  }
}
