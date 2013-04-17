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
  static $event_codes = array ("Q22", "ZV1");

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
   * @param CHL7v2PatientDemographicsAndVisitResponse $ack     Acknowledgement
   * @param CPatient                                  $patient Person
   * @param array                                     $data    Nodes data
   *
   * @return null|string
   */
  function handle(CHL7v2PatientDemographicsAndVisitResponse $ack, CPatient $patient, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    $quantity_limited_request = $this->getQuantityLimitedRequest($data["RCP"]);
    $quantity_limited_request = $quantity_limited_request ? $quantity_limited_request : 100;

    $ds    = $patient->getDS();
    $where = array();
    foreach ($this->getRequestPatient($data["QPD"]) as $field => $value) {
      if ($value == "") {
        continue;
      }

      $value = preg_replace("/[^a-z\*]/i", "_", $value);
      $value = preg_replace("/\*+/", "%", $value);
      $where[$field] = $ds->prepare("LIKE %", $value);
    }

    $ljoin = null;
    // Requ�te sur un IPP
    if ($identifier_list = $this->getRequestPatientIdentifierList($data["QPD"])) {
      $ljoin[10] = "id_sante400 AS id1 ON id1.object_id = patients.patient_id";
      $where[] = "`id1`.`object_class` = 'CPatient'";

      if (isset($identifier_list["id_number"])) {
        $id_number = $identifier_list["id_number"];
        $where[]   = $ds->prepare("id1.id400 = %", $id_number);
      }
    }

    // Requ�te sur un NDA
    if ($identifier_list = $this->getRequestSejourIdentifierList($data["QPD"])) {
      $ljoin["sejour"] = "`sejour`.`patient_id` = `patients`.`patient_id`";
      $ljoin[]         = "id_sante400 AS id2 ON `id2`.`object_id` = `sejour`.`sejour_id`";
      if (isset($identifier_list["id_number"])) {
        $id_number = $identifier_list["id_number"];
        $where[]   = $ds->prepare("id2.id400 = %", $id_number);
      }
    }

    $i = 1;

    $domains = array();
    foreach ($this->getQPD8s($data["QPD"]) as $_QPD8) {
      // Requ�te sur un domaine particulier
      $domains_returned_namespace_id = $_QPD8["domains_returned_namespace_id"];
      // Requ�te sur un OID particuli�
      $domains_returned_universal_id = $_QPD8["domains_returned_universal_id"];

      $domain = new CDomain();
      if ($domains_returned_namespace_id) {
        $domain->tag = $domains_returned_namespace_id;
      }
      if ($domains_returned_universal_id) {
        $domain->OID = $domains_returned_universal_id;
      }

      if ($domain->tag || $domain->OID) {
        $domain->loadMatchingObject();
      }

      $value = $domain->OID ? $domain->OID : $domain->tag;

      // Cas o� le domaine n'est pas retrouv�
      if (!$domain->_id) {
        return $exchange_ihe->setPDRAE($ack, null, $value);
      }

      $domains[] = $domain;

      if ($domains_returned_namespace_id) {
        $ljoin[20+$i] = "id_sante400 AS id$i ON id$i.object_id = patients.patient_id";
        $where[]   = $ds->prepare("id$i.tag = %", $domains_returned_namespace_id);

        $i++;
      }
    }

    // Pointeur pour continuer
    if (isset($patient->_pointer)) {
      // is_numeric
      $where["patient_id"] = $ds->prepare(" > %", $patient->_pointer);
    }

    $order = "patient_id ASC";

    $patients = array();
    if (!empty($where)) {
      $patients = $patient->loadList($where, $order, $quantity_limited_request, null, $ljoin);
    }

    return $exchange_ihe->setPDRAA($ack, $patients, null, $domains);
  }

  /**
   * Get PID QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return array
   */
  function getRequestPatient(DOMNode $node) {
    $PID = array();

    // Patient Name
    if ($PID_5_1_1 = $this->getDemographicsFields($node, "CPatient", "5.1.1")) {
      $PID = array_merge($PID, array("nom" => $PID_5_1_1));
    }
    if ($PID_5_2 = $this->getDemographicsFields($node, "CPatient", "5.2")) {
      $PID = array_merge($PID, array("prenom" => $PID_5_2));
    }

    // Maiden name
    if ($PID_6_1_1 = $this->getDemographicsFields($node, "CPatient", "6.1.1")) {
      $PID = array_merge($PID, array("nom_jeune_fille" => $PID_6_1_1));
    }

    // Date of birth"
    if ($PID_7_1 = $this->getDemographicsFields($node, "CPatient", "7.1")) {
      $PID = array_merge($PID, array("naissance" => CMbDT::date($PID_7_1)));
    }

    // Patient Adress
    if ($PID_11_3 = $this->getDemographicsFields($node, "CPatient", "11.3")) {
      $PID = array_merge($PID, array("ville" => $PID_11_3));
    }
    if ($PID_11_5 = $this->getDemographicsFields($node, "CPatient", "11.5")) {
      $PID = array_merge($PID, array("cp" => $PID_11_5));
    }

    return $PID;
  }

  /**
   * Get PID.3 QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function getRequestPatientIdentifierList(DOMNode $node) {
    $QPD = array(
      "id_number"            => $this->getDemographicsFields($node, "CPatient", "3.1"),
      /*"namespace_id"         => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "universal_id"         => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "universal_id_type"    => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "identifier_type_code" => $this->getDemographicsFields($node, "CPatient", "3.1"),*/
    );

    CMbArray::removeValue("", $QPD);

    return $QPD;
  }

  /**
   * Get PID.3 QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function getRequestSejourIdentifierList(DOMNode $node) {
    $QPD = array(
      "id_number" => $this->getDemographicsFields($node, "CPatient", "18.1")
    );

    CMbArray::removeValue("", $QPD);

    return $QPD;
  }

  /**
   * Get QPD.8 element
   *
   * @param DOMNode $node QPD element
   *
   * @return array()
   */
  function getQPD8s(DOMNode $node) {
    $QPD8s = array();

    foreach ($this->queryNodes("QPD.8", $node) as $_QPD_8) {
      $QPD8s[] = array (
        "domains_returned_namespace_id"      => $this->queryTextNode("CX.4/HD.1", $_QPD_8),
        "domains_returned_universal_id"      => $this->queryTextNode("CX.4/HD.2", $_QPD_8),
        "domains_returned_universal_id_type" => $this->queryTextNode("CX.4/HD.3", $_QPD_8)
      );
    }

    return $QPD8s;
  }

  /**
   * Get PV1 QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function getRequestSejour(DOMNode $node) {


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
