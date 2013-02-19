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
  /**
   * @var CHL7Event|null
   */
  var $event              = null;
  /**
   * @var CHL7v2EventACK
   */

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
  var $hl7_error_code     = null;
  var $severity           = null;
  var $comments           = null;
  var $objects            = null;
  var $QPD8_error         = null;

  /**
   * @var CExchangeIHE
   */
  var $_ref_exchange_ihe  = null;

  /**
   * Construct
   *
   * @param CHL7Event $event Event HL7
   *
   * @return CHL7v2PatientDemographicsAndVisitResponse
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
   * @param string     $ack_code       Acknowledgment code
   * @param null       $hl7_error_code HL7 error code
   * @param string     $severity       Severity
   * @param array|null $objects        Objects
   * @param array|null $QPD8_error     QPD-8 that contained the unrecognized domain
   *
   * @return null|string
   */
  function generateAcknowledgment($ack_code, $hl7_error_code = null, $severity = "E", $objects = null, $QPD8_error = null) {
    $this->ack_code       = $ack_code;
    $this->hl7_error_code = $hl7_error_code;
    $this->severity       = $severity;
    $this->objects        = $objects;
    $this->QPD8_error     = $QPD8_error;

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
