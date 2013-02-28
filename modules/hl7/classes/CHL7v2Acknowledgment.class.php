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
  public $event;
  /**
   * @var CHL7v2EventACK
   */
  public $event_ack;

  /**
   * @var CHL7v2Message
   */
  public $message;
  /**
   * @var CHL7v2MessageXML
   */
  public $dom_message;
  
  public $message_control_id;
  public $ack_code;
  public $mb_error_codes;
  public $hl7_error_code;
  public $severity;
  public $comments;
  /**
   * @var CMbObject
   */
  public $object;

  /**
   * @var CExchangeIHE
   */
  public $_ref_exchange_ihe;
  public $_mb_error_code;

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
