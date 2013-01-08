<?php
/**
 * Patient Demographics and Visit Response
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CHL7v2PatientDemographicsAndVisitResponse
 * Patient Demographics and Visit Response, message XML HL7
 */
class CHL7v2PatientDemographicsAndVisitResponse implements CHL7Acknowledgment {
  var $event              = null;
  var $event_ack          = null;
  
  var $message            = null;
  var $dom_message        = null;
  
  var $message_control_id = null;
  var $ack_code           = null;
  var $mb_error_codes     = null;
  var $hl7_error_code     = null;
  var $severity           = null;
  var $comments           = null;
  var $objects            = null;
  
  var $_ref_exchange_ihe  = null;
  var $_mb_error_code     = null;
    
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
   * @return null
   */
  function generateAcknowledgment($ack_code, $mb_error_codes, $hl7_error_code = null, $severity = "E", $comments = null, $objects = null) {
    $this->ack_code       = $ack_code;
    $this->mb_error_codes = $mb_error_codes;
    $this->hl7_error_code = $hl7_error_code;
    $this->severity       = $severity;
    $this->comments       = CMbString::convertHTMLToXMLEntities($comments);
    $this->objects        = $objects;

    $this->event->_exchange_ihe = $this->_ref_exchange_ihe;

    $this->event_ack = new CHL7v2EventRSP($this->event);
    $this->event_ack->build($this);
    $this->event_ack->flatten();

    $this->event_ack->msg_hl7 = utf8_encode($this->event_ack->msg_hl7);
    
    return $this->event_ack->msg_hl7;
  }

  /**
   * Get statut acknowledgment
   *
   * @return null
   */
  function getStatutAcknowledgment() {
  }
}
