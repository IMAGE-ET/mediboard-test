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
      if (!$value) {
        continue;
      }

      $value = preg_replace("/[^a-z]/i", "_", $value);
      $where[$field] = $ds->prepareLike($value);
    }

    $ljoin = null;
    // Requête sur un IPP
    if ($identifier_list = $this->getRequestPatientIdentifierList($data["QPD"])) {
      $ljoin[10] = "id_sante400 AS id1 ON id1.object_id = patients.patient_id";
      $where[] = "`id1`.`object_class` = 'CPatient'";

      if (isset($identifier_list["id_number"])) {
        $id_number = $identifier_list["id_number"];
        $where[]   = $ds->prepare("id1.id400 = %", $id_number);
      }
    }

    // Requête sur un NDA
    if ($identifier_list = $this->getRequestSejourIdentifierList($data["QPD"])) {
      $ljoin["sejour"] = "`sejour`.`patient_id` = `patients`.`patient_id`";
      $ljoin[]         = "id_sante400 AS id2 ON `id2`.`object_id` = `sejour`.`sejour_id`";
      if (isset($identifier_list["id_number"])) {
        $id_number = $identifier_list["id_number"];
        $where[]   = $ds->prepare("id2.id400 = %", $id_number);
      }
    }

    $QPD8 = $this->getQPD8($data["QPD"]);
    // Requête sur un domaine particulier qui est inconnu
    if ($QPD8) {
      $domains_returned_namespace_id = $QPD8["domains_returned_namespace_id"];
      if ($domains_returned_namespace_id) {
        $idex               = new CIdSante400();
        $idex->object_class = "CPatient";
        $idex->tag          = $domains_returned_namespace_id;
        $count = $idex->countMatchingListEsc();

        // Si aucun domaine n'est retrouvé on retourne une erreur
        if ($count == 0) {
          return $exchange_ihe->setPDRAE($ack, null, $QPD8);
        }
      }
    }

    // Requête sur un domaine particulier
    if ($QPD8) {
      $ljoin[10] = "id_sante400 AS id1 ON id1.object_id = patients.patient_id";
      if ($domains_returned_namespace_id) {
        $where[]   = $ds->prepare("id1.tag = %", $domains_returned_namespace_id);
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

    return $exchange_ihe->setPDRAA($ack, $patients);
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
      $PID = array_merge($PID, array("naissance" => mbDate($PID_7_1)));
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
    return array(
      "id_number"            => $this->getDemographicsFields($node, "CPatient", "3.1"),
      /*"namespace_id"         => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "universal_id"         => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "universal_id_type"    => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "identifier_type_code" => $this->getDemographicsFields($node, "CPatient", "3.1"),*/
    );
  }

  /**
   * Get PID.3 QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function getRequestSejourIdentifierList(DOMNode $node) {
    return array(
      "id_number" => $this->getDemographicsFields($node, "CPatient", "18.1")
    );
  }

  /**
   * Get QPD.8 element
   *
   * @param DOMNode $node QPD element
   *
   * @return string
   */
  function getQPD8(DOMNode $node) {
    return array (
      "domains_returned_namespace_id"      => $this->queryTextNode("QPD.8/CX.4/HD.1", $node),
      "domains_returned_universal_id"      => $this->queryTextNode("QPD.8/CX.4/HD.2", $node),
      "domains_returned_universal_id_type" => $this->queryTextNode("QPD.8/CX.4/HD.3", $node)
    );
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
