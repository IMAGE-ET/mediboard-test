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
 * Class CHL7v2Acknowledgment 
 * Acknowledgment v2 HL7
 */
class CHL7v2Acknowledgment implements CHL7Acknowledgment {
  /**
   * @var CHL7Event|null
   */
  var $event              = null;
  /**
   * @var CHL7v2EventACK
   */
  var $event_ack          = null;

  /**
   * @var CHL7v2Message
   */
  var $message            = null;
  /**
   * @var CHL7v2MessageXML
   */
  var $dom_message        = null;
  
  var $message_control_id = null;
  var $ack_code           = null;
  var $mb_error_codes     = null;
  var $hl7_error_code     = null;
  var $severity           = null;
  var $comments           = null;
  /**
   * @var CMbObject
   */
  var $object             = null;

  /**
   * @var CExchangeIHE
   */
  var $_ref_exchange_ihe  = null;
  var $_mb_error_code     = null;

  /**
   * Construct
   *
   * @param CHL7Event $event Event HL7
   *
   * @return CHL7v2Acknowledgment
   */
  function __construct(CHL7Event $event = null) {
    $this->event = $event;
  }

  /**
   * Handle acknowledgment
   *
   * @param string $ack_hl7 HL7 acknowledgment
   *
   * @return DOMDocument
   */
  function handle($ack_hl7) {
    $this->message = new CHL7v2Message();
    $this->message->parse($ack_hl7);
    $this->dom_message = $this->message->toXML();
    
    return $this->dom_message;
  }

  /**
   * Generate acknowledgment
   *
   * @param string $ack_code       Acknowledgment code
   * @param string $mb_error_codes Mediboard error code
   * @param null   $hl7_error_code HL7 error code
   * @param string $severity       Severity
   * @param null   $comments       Comments
   * @param null   $object         Object
   *
   * @return null|string
   */
  function generateAcknowledgment(
      $ack_code, $mb_error_codes, $hl7_error_code = null, $severity = "E", $comments = null, $object = null
  ) {
    $this->ack_code       = $ack_code;
    $this->mb_error_codes = $mb_error_codes;
    $this->hl7_error_code = $hl7_error_code;
    $this->severity       = $severity;
    $this->comments       = CMbString::convertHTMLToXMLEntities($comments);
    $this->object         = $object;

    $this->event->_exchange_ihe = $this->_ref_exchange_ihe;

    $this->event_ack = new CHL7v2EventACK($this->event);
    $this->event_ack->build($this);
    $this->event_ack->flatten();
    
    $this->event_ack->msg_hl7 = utf8_encode($this->event_ack->msg_hl7);
    
    return $this->event_ack->msg_hl7;
  }

  /**
   * Get acknowledgment status
   *
   * @return string
   */
  function getStatutAcknowledgment() {
    $xpath = new CHL7v2MessageXPath($this->dom_message);

    return $xpath->queryTextNode("//MSA/MSA.1");
  }
}
