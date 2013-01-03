<?php
/**
 * Generate patient demographics response
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2GeneratePatientDemographicsResponse
 * Receive patient demographics response, message XML HL7
 */
class CHL7v2GeneratePatientDemographicsResponse extends CHL7v2MessageXML {
  /**
   * @var string
   */
  static $event_codes = "Q22 ZV1";

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data  = array();

    $this->queryNode("QPD", null, $data, true);

    $this->queryNode("RCP", null, $data, true);

    return $data;
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack     Acknowledgement
   * @param CPatient           $patient Person
   * @param array              $data    Nodes data
   *
   * @return null|string
   */
  function handle(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    $quantity_limited_request = $this->getQuantityLimitedRequest($data["RCP"]);

    $this->getQPD($data["QPD"], $patient);


  }

  /**
   * Get QPD element
   *
   * @param DOMNode  $node    QPD element
   * @param CPatient $patient Person
   *
   * @return string
   */
  function getQPD(DOMNode $node, CPatient $patient) {
    $this->addQPD3PID($node, $patient);
  }

  /**
   * Get PID QPD element
   *
   * @param DOMNode  $node    QPD element
   * @param CPatient $patient Person
   *
   * @return string
   */
  function addQPD3PID(DOMNode $node, CPatient $patient) {

  }

  /**
   * Get quantity limited request
   *
   * @param DOMNode $node RCP element
   *
   * @return int
   */
  function getQuantityLimitedRequest(DOMNode $node) {
    return $this->queryTextNode("RCP.2/CQ.1", $node);
  }
}
