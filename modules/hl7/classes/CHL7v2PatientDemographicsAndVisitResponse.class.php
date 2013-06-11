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
  public $event;

  /** @var CHL7v2EventACK */


  /** @var CHL7v2Message */
  public $message;

  /** @var CHL7v2MessageXML */
  public $dom_message;
  
  public $message_control_id;
  public $ack_code;
  public $hl7_error_code;
  public $severity;
  public $comments;
  public $objects;
  public $QPD8_error;
  public $domains;


  /** @var CExchangeHL7v2 */
  public $_ref_exchange_hl7v2;

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
   * @param string         $ack_code       Acknowledgment code
   * @param null           $hl7_error_code HL7 error code
   * @param string         $severity       Severity
   * @param array|null     $objects        Objects
   * @param array|null     $QPD8_error     QPD-8 that contained the unrecognized domain
   * @param CDomain[]|null $domains        QPD-8 domains
   *
   * @return null|string
   */
  function generateAcknowledgment(
      $ack_code, $hl7_error_code = null, $severity = "E", $objects = null, $QPD8_error = null, $domains = null
  ) {
    $this->ack_code       = $ack_code;
    $this->hl7_error_code = $hl7_error_code;
    $this->severity       = $severity;
    $this->objects        = $objects;
    $this->QPD8_error     = $QPD8_error;
    $this->domains        = $domains;

    $this->event->_exchange_hl7v2 = $this->_ref_exchange_hl7v2;

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
