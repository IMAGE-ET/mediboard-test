<?php

/**
 * Patient Administration HL7v3
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPA
 * Patient Administration HL7v3
 */
class CHL7v3EventPRPA extends CHL7v3Event implements CHL7EventPRPA {

  /** @var string */
  public $interaction_id = null;

  /**
   * Construct
   *
   * @return \CHL7v3EventPRPA
   */
  function __construct() {
    parent::__construct();

    $this->event_type = "PRPA";
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);

    // Header
    $this->addHeader();

    // Receiver
    $this->addReceiver();

    // Sender
    $this->addSender();
  }

  /**
   * Get interaction
   *
   * @return string|void
   */
  function getInteractionID() {
  }

  /**
   * Add header
   *
   * @return void
   */
  function addHeader() {
    $dom            = $this->dom;
    $exchange_hl7v3 = $this->_exchange_hl7v3;

    $root = $dom->addElement($dom, $this->getInteractionID());
    $dom->addNameSpaces();

    // id
    $id = $dom->addElement($root, "id");
    $dom->addAttribute($id, "extension", "ID");
    $dom->addAttribute($id, "root", "OID");

    // creationTime
    $creationTime = $dom->addElement($root, "creationTime");
    $dom->addAttribute($creationTime, "value", CHL7v3MessageXML::dateTime());

    // interactionId
    $interactionId = $dom->addElement($root, "interactionId");
    $dom->addAttribute($interactionId, "extension", $this->getInteractionID());
    $dom->addAttribute($interactionId, "root", "2.16.840.1.113883.1.6");

    // processingCode
    $processingCode = $dom->addElement($root, "processingCode");
    $instance_role  = CAppUI::conf("instance_role") == "prod" ? "P" : "D";
    $dom->addAttribute($processingCode, "code", $instance_role);

    // processingModeCode
    $processingModeCode = $dom->addElement($root, "processingModeCode");
    $dom->addAttribute($processingModeCode, "code", "T");

    // acceptAckCode
    $acceptAckCode = $dom->addElement($root, "acceptAckCode");
    $dom->addAttribute($acceptAckCode, "code", "AL");
  }

  /**
   * Add receiver
   *
   * @return void
   */
  function addReceiver() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $receiver = $dom->addElement($root, "receiver");
    $dom->addAttribute($receiver, "typeCode", "RCV");

    $this->addDevice($receiver, $this->_receiver);
  }

  /**
   * Add sender
   *
   * @return void
   */
  function addSender() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $sender = $dom->addElement($root, "sender");
    $dom->addAttribute($sender, "typeCode", "SND");

    $this->addDevice($sender);
  }

  /**
   * Add device
   *
   * @param DOMNode       $elParent Parent element
   * @param CInteropActor $actor    Actor
   *
   * @return void
   */
  function addDevice(DOMNode $elParent, CInteropActor $actor = null) {
    $dom = $this->dom;

    // device
    $device = $dom->addElement($elParent, "device");
    $dom->addAttribute($device, "classCode", "DEV");
    $dom->addAttribute($device, "determinerCode", "INSTANCE");

    // id
    $id = $dom->addElement($device, "id");
    $dom->addAttribute($id, "root", $actor ? $actor->OID : CAppUI::conf("mb_oid"));

    // softwareName
    $dom->addElement($device, "softwareName", $actor ? $actor->nom : CAppUI::conf("mb_id"));
  }

  /**
   * Add control act process
   *
   * @param CPatient $patient Patient
   *
   * @return DOMElement
   */
  function addControlActProcess(CPatient $patient) {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $controlActProcess = $dom->addElement($root, "controlActProcess");
    $dom->addAttribute($controlActProcess, "classCode", "CACT");
    $dom->addAttribute($controlActProcess, "moodCode", "EVN");

    return $controlActProcess;
  }
}