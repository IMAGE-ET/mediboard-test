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
  /**
   * Construct
   *
   * @param CHL7Event $trigger_event Trigger event
   *
   * @return CHL7v2EventACK
   */
  function __construct(CHL7Event $trigger_event) { 
    $this->event_type  = "ACK";
    $this->version     = $trigger_event->message->version;
    
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $trigger_event->code, $this->event_type
      )
    );

    $this->_exchange_hl7v2 = $trigger_event->_exchange_hl7v2;
    $this->_receiver       = $trigger_event->_exchange_hl7v2->_ref_receiver;
    $this->_sender         = $trigger_event->_exchange_hl7v2->_ref_sender;
  }

  /**
   * Build event
   *
   * @param CHL7Acknowledgment $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
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
      if ($this->version < "2.5") {
        $object->_mb_error_code = reset($object->mb_error_codes);
        $this->addERR($object);
      }
      else {
        foreach ($object->mb_error_codes as $_mb_error_code) {
          $object->_mb_error_code = $_mb_error_code;
          $this->addERR($object);
        }
      }
    }
    else {
      if ($object->mb_error_codes) {
        $object->_mb_error_code = $object->mb_error_codes;
        $this->addERR($object);
      }
    }

    $trigger_event = $object->event;
    // Validation error
    if ($errors = $trigger_event->message->errors) {
      foreach ($errors as $_error) {
        $this->addERR($object, $_error);
      }
    }
  }

  /**
   * MSH - Represents an HL7 MSH message segment (Message Header)
   *
   * @return void
   */
  function addMSH() {
    /** @var CHL7v2SegmentMSH $MSH */
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }
  
  /**
   * SFT - Represents an HL7 SFT message segment (Software Segment)
   *
   * @return void
   */
  function addSFT() {
  }
  
  /**
   * MSA - Represents an HL7 MSA message segment (Message Acknowledgment)
   *
   * @param CHL7Acknowledgment $acknowledgment Acknowledgment
   *
   * @return void
   */
  function addMSA(CHL7Acknowledgment $acknowledgment) {
    $MSA = CHL7v2Segment::create("MSA", $this->message);
    $MSA->acknowledgment = $acknowledgment;
    $MSA->build($this);
  }
  
  /**
   * ERR - Represents an HL7 ERR message segment (Error)
   *
   * @param CHL7Acknowledgment $acknowledgment Acknowledgment
   * @param CHL7v2Error|null   $error          Error HL7
   *
   * @return void
   */
  function addERR(CHL7Acknowledgment $acknowledgment, $error = null) {
    $ERR = CHL7v2Segment::create("ERR", $this->message);
    $ERR->acknowledgment = $acknowledgment;
    $ERR->error = $error;
    $ERR->build($this);
  }

  /**
   * Get the message as a string
   *
   * @return string
   */
  function flatten() {
    $this->msg_hl7 = $this->message->flatten();
    $this->message->validate();
  }
}