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

    $patient->loadMatchingPatient();
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
    // PID
    $this->addQPD3PID($node, $patient);

    // PV1
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
    // Patient Name
    $patient->nom             = $this->getDemographicsFields($node, $patient, "5.1.1");
    $patient->prenom          = $this->getDemographicsFields($node, $patient, "5.2");

    // Maiden name
    $patient->nom_jeune_fille = $this->getDemographicsFields($node, $patient, "6.1.1");

    // Date of birth
    $patient->naissance       = $this->getDemographicsFields($node, $patient, "7.1");

    // Patient Adress
    $patient->ville           = $this->getDemographicsFields($node, $patient, "11.3");
    $patient->cp              = $this->getDemographicsFields($node, $patient, "11.5");
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

  /**
   * Get QPD-3 demographics fields
   *
   * @param DOMNode   $node   Node
   * @param CMbObject $object Object
   * @param string    $field  The number of a field
   *
   * @return array
   */
  function getDemographicsFields(DOMNode $node, CMbObject $object, $field) {

    $seg = null;
    switch ($object->_class) {
      case "CPatient" :
        $seg = "PID";
        break;
      case "CSejour" :
        $seg = "PV1";
        break;
    }

    foreach ($this->queryNodes("QPD.3", $node) as $_QPD_3) {
      if ("@$seg.$field" == $this->queryTextNode("QIP.1", $_QPD_3)) {
        return $this->queryTextNode("QIP.2", $_QPD_3);
      }
    }
  }
}
