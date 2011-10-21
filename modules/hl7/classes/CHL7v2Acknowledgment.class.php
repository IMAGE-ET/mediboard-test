<?php

/**
 * Acknowledgment v2 HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2Acknowledgment 
 * Acknowledgment v2 HL7
 */
class CHL7v2Acknowledgment implements CHL7Acknowledgment {
  var $ack            = null;
  var $event          = null;
  
  var $ack_code       = null;
  var $mb_error_code  = null;
  var $hl7_error_code = null;
  var $severity       = null;
  var $comments       = null;
  var $object         = null;
  
  var $code                = null;
  var $message_control_id  = null;
  var $_ref_exchange_ihe   = null;
  
  function __construct(CHL7Event $event) {
    $this->event = $event;
  }
  
  function generateAcknowledgment($ack_code, $mb_error_code, $hl7_error_code = null, $severity = "E", $comments = null, $object = null) {
    $this->ack_code       = $ack_code;
    $this->mb_error_code  = $mb_error_code;
    $this->hl7_error_code = $hl7_error_code;
    $this->severity       = $severity;
    $this->comments       = $comments;
    $this->object         = $object;

    $this->event->_exchange_ihe = $this->_ref_exchange_ihe;

    $event_ack = new CHL7v2EventACK($this->event);
    $event_ack->build($this);
    $event_ack->flatten();
    
    return $event_ack->msg_hl7;
  }
}

?>