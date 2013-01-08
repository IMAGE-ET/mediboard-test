<?php

/**
 * Represents a ACK message structure (see chapter 2.14.1) HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventACK 
 * Represents a ACK message structure (see chapter 2.14.1)
 */
class CHL7v2EventACK extends CHL7v2Event implements CHL7EventACK {
  function __construct(CHL7Event $trigger_event) { 
    $this->event_type  = "ACK";
    $this->version     = $trigger_event->message->version;
    
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $trigger_event->code, $this->event_type
      )
    );

    $this->_exchange_ihe = $trigger_event->_exchange_ihe;
    $this->_receiver     = $trigger_event->_exchange_ihe->_ref_receiver;
    $this->_sender       = $trigger_event->_exchange_ihe->_ref_sender;
  }
  
  function build($object) {
    // Création du message HL7
    $this->message          = new CHL7v2Message();
    $this->message->version = $this->version;
    $this->message->name    = $this->msg_codes;
    
    // Message Header 
    $this->addMSH();
    
    // Software Segment
    $this->addSFT();
    
    // Message Acknowledgment
    $this->addMSA($object);
    
    // Error
    if (is_array($object->mb_error_codes)) {
      foreach ($object->mb_error_codes as $_mb_error_code) {
        $object->_mb_error_code = $_mb_error_code;
        $this->addERR($object);
      }
    }
    else {
      $object->_mb_error_code = $object->mb_error_codes;
      $this->addERR($object);
    }  
    
    $trigger_event = $object->event; 
    // Validation error
    if ($errors = $trigger_event->message->errors) {
      foreach ($errors as $_error) {
        $this->addERR($object, $_error);
      }
    }
  }
  
  /*
   * MSH - Represents an HL7 MSH message segment (Message Header) 
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }
  
  /*
   * SFT - Represents an HL7 SFT message segment (Software Segment)
   */
  function addSFT() {}
  
  /*
   * MSA - Represents an HL7 MSA message segment (Message Acknowledgment)
   */
  function addMSA(CHL7Acknowledgment $acknowledgment) {
    $MSA = CHL7v2Segment::create("MSA", $this->message);
    $MSA->acknowledgment = $acknowledgment;
    $MSA->build($this);
  }
  
  /*
   * ERR - Represents an HL7 ERR message segment (Error)
   */
  function addERR(CHL7Acknowledgment $acknowledgment, $error = null) {
    $ERR = CHL7v2Segment::create("ERR", $this->message);
    $ERR->acknowledgment = $acknowledgment;
    $ERR->error = $error;
    $ERR->build($this);
  }
  
  function flatten() {
    $this->msg_hl7 = $this->message->flatten();
    $this->message->validate();
  }
}

?>