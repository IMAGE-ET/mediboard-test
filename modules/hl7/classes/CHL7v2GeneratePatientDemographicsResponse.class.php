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
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    $quantity_limited_request = $this->getQuantityLimitedRequest($data["RCP"]);
    $quantity_limited_request = $quantity_limited_request ? $quantity_limited_request : 100;

    $ds    = $patient->_spec->ds;
    $where = array();
    foreach ($this->getQPD($data["QPD"]) as $field => $value) {
      if (!$value) {
        continue;
      }

      $value = preg_replace("/[^a-z]/i", "_", $value);
      $where[$field] = $ds->prepareLike($value);
    }

    if (isset($patient->_pointer)) {
      // is_numeric
      $where["patient_id"] = $ds->prepareLike(" >%", $patient->_pointer);
    }

    $order = "patient_id ASC";

    $patients = $patient->loadList($where, $order, $quantity_limited_request);

    return $exchange_ihe->setPDRAA($ack, "I001", null, $patients);
  }

  /**
   * Get QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function getQPD(DOMNode $node) {
    $datas = array();

    // PID
    $datas = array_merge($datas, $this->addQPD3PID($node));

    // PV1

    return $datas;
  }

  /**
   * Get PID QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function addQPD3PID(DOMNode $node) {
    return array(
      // Patient Name
      "nom"             => $this->getDemographicsFields($node, "CPatient", "5.1.1"),
      "prenom"          => $this->getDemographicsFields($node, "CPatient", "5.2"),

    // Maiden name
      "nom_jeune_fille" => $this->getDemographicsFields($node, "CPatient", "6.1.1"),

    // Date of birth"
      "naissance"       => $this->getDemographicsFields($node, "CPatient", "7.1"),

    // Patient Adress
      "ville"           => $this->getDemographicsFields($node, "CPatient", "11.3"),
      "cp "             => $this->getDemographicsFields($node, "CPatient", "11.5")
    );
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
   * @param DOMNode $node         Node
   * @param string  $object_class Object Class
   * @param string  $field        The number of a field
   *
   * @return array
   */
  function getDemographicsFields(DOMNode $node, $object_class, $field) {

    $seg = null;
    switch ($object_class) {
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
