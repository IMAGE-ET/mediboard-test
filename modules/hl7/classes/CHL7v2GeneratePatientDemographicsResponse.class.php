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

  /** @var string */
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

    $this->queryNode("DSC", null, $data, true);

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
    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender       = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    $ds = $patient->getDS();

    $where = array();
    foreach ($this->getRequestPatient($data["QPD"]) as $field => $value) {
      if ($value == "") {
        continue;
      }

      if (!in_array($field, array("naissance", "cp"))) {
        $value = preg_replace("/[^a-z\d\*]/i", "_", $value);
        $value = preg_replace("/\*+/", "%", $value);
      }

      $where["patients.$field"] = $ds->prepare("LIKE %", $value);
    }

    $ljoin = null;

    $identifier_list = $this->getRequestPatientIdentifierList($data["QPD"]);
    if (count(array_filter($identifier_list)) > 0) {
      $ljoin[10] = "id_sante400 AS id_pat_list ON id_pat_list.object_id = patients.patient_id";
      $where[] = "`id_pat_list`.`object_class` = 'CPatient'";
      // Requ�te sur un IPP
      if (!empty($identifier_list["id_number"])
          && empty($identifier_list["namespace_id"])
          && empty($identifier_list["universal_id"])
          && empty($identifier_list["universal_id_type"])
      ) {
        $where[] = $ds->prepare("id_pat_list.id400 = %", $identifier_list["id_number"]);
      }

      if (!empty($identifier_list["id_number"])
          && (!empty($identifier_list["namespace_id"])
          || !empty($identifier_list["universal_id"]))
      ) {

        $namespace_id = $identifier_list["namespace_id"];
        $universal_id = $identifier_list["universal_id"];

        $domain = new CDomain();
        if ($namespace_id) {
          $domain->namespace_id = $namespace_id;
        }
        if ($universal_id) {
          $domain->OID = $universal_id;
        }

        if ($domain->tag || $domain->OID) {
          $domain->loadMatchingObject();
        }

        $where[] = $ds->prepare("id_pat_list.id400 = %", $identifier_list["id_number"]);
        $where[] = $ds->prepare("id_pat_list.tag = %", $domain->tag);
      }

      if (empty($identifier_list["id_number"])
          && (!empty($identifier_list["namespace_id"])
          || !empty($identifier_list["universal_id"]))
      ) {
        $namespace_id = $identifier_list["namespace_id"];
        $universal_id = $identifier_list["universal_id"];

        $domain = new CDomain();
        if ($namespace_id) {
          $domain->namespace_id = $namespace_id;
        }
        if ($universal_id) {
          $domain->OID = $universal_id;
        }

        if ($domain->namespace_id || $domain->OID) {
          $domain->loadMatchingObject();

          $where[] = $ds->prepare("id_pat_list.tag = %", $domain->tag);
        }
      }
    }

    $request_admit = false;
    // Requ�te sur un NDA
    $identifier_list = $this->getRequestSejourIdentifierList($data["QPD"]);
    if (count(array_filter($identifier_list)) > 0) {
        $ljoin[100] = "sejour ON `patients`.`patient_id` = `sejour`.`patient_id`";
      $ljoin[10] = "id_sante400 AS id_sej_list ON id_sej_list.object_id = sejour.sejour_id";
      $where[] = "`id_sej_list`.`object_class` = 'CSejour'";
      // Requ�te sur un IPP
      if (!empty($identifier_list["id_number"])
          && empty($identifier_list["namespace_id"])
          && empty($identifier_list["universal_id"])
          && empty($identifier_list["universal_id_type"])
      ) {
        $where[] = $ds->prepare("id_sej_list.id400 = %", $identifier_list["id_number"]);
      }

      if (!empty($identifier_list["id_number"])
          && (!empty($identifier_list["namespace_id"])
          || !empty($identifier_list["universal_id"]))
      ) {

        $namespace_id = $identifier_list["namespace_id"];
        $universal_id = $identifier_list["universal_id"];

        $domain = new CDomain();
        if ($namespace_id) {
          $domain->namespace_id = $namespace_id;
        }
        if ($universal_id) {
          $domain->OID = $universal_id;
        }

        if ($domain->namespace_id || $domain->OID) {
          $domain->loadMatchingObject();
        }

        $where[] = $ds->prepare("id_sej_list.id400 = %", $identifier_list["id_number"]);
        $where[] = $ds->prepare("id_sej_list.tag = %", $domain->tag);
      }

      if (empty($identifier_list["id_number"])
          && (!empty($identifier_list["namespace_id"])
          || !empty($identifier_list["universal_id"]))
      ) {
        $namespace_id = $identifier_list["namespace_id"];
        $universal_id = $identifier_list["universal_id"];

        $domain = new CDomain();
        if ($namespace_id) {
          $domain->namespace_id = $namespace_id;
        }
        if ($universal_id) {
          $domain->OID = $universal_id;
        }

        if ($domain->namespace_id || $domain->OID) {
          $domain->loadMatchingObject();

          $where[] = $ds->prepare("id_sej_list.tag = %", $domain->tag);
        }
      }
    }

    foreach ($this->getRequestSejour($data["QPD"]) as $field => $value) {
      if ($value == "") {
        continue;
      }

      $value = preg_replace("/[^a-z\*]/i", "_", $value);
      $value = preg_replace("/\*+/", "%", $value);
      $where["sejour.$field"] = $ds->prepare("LIKE %", $value);

      $request_admit= true;
    }

    if ($other_request = $this->getOtherRequestSejour($data["QPD"])) {
      $where = array_merge($other_request, $where);

      $request_admit= true;
    }

    $i = 1;

    $domains = array();
    foreach ($this->getQPD8s($data["QPD"]) as $_QPD8) {
      // Requ�te sur un domaine particulier
      $domains_returned_namespace_id = $_QPD8["domains_returned_namespace_id"];
      // Requ�te sur un OID particulier
      $domains_returned_universal_id = $_QPD8["domains_returned_universal_id"];

      $domain = new CDomain();
      if ($domains_returned_namespace_id) {
        $domain->namespace_id = $domains_returned_namespace_id;
      }
      if ($domains_returned_universal_id) {
        $domain->OID = $domains_returned_universal_id;
      }

      if ($domain->namespace_id || $domain->OID) {
        $domain->loadMatchingObject();
      }

      $value = $domain->OID ? $domain->OID : $domain->tag;

      // Cas o� le domaine n'est pas retrouv�
      if (!$domain->_id) {
        return $exchange_hl7v2->setPDRAE($ack, null, $value);
      }

      $domains[] = $domain;

      if ($domains_returned_namespace_id) {
        $ljoin[20+$i] = "id_sante400 AS id$i ON id$i.object_id = patients.patient_id";
        $where[]   = $ds->prepare("id$i.tag = %", $domain->tag);

        $i++;
      }
    }

    $quantity_limited_request = $this->getQuantityLimitedRequest($data["RCP"]);
    $limit_quantity = !!$quantity_limited_request;
    $quantity_limited_request = $quantity_limited_request ? $quantity_limited_request : 100;

    $pointer = null;
    if (isset($data["DSC"])) {
      $pointer = $this->getContinuationPointer($data["DSC"]);
    }

    $objects = array();
    if (!$request_admit) {
      // Pointeur pour continuer
      if ($pointer) {
        $patient->_pointer = $pointer;
        // is_numeric
        $where["patients.patient_id"] = $ds->prepare(" > %", $pointer);
      }

      $order = "patients.patient_id ASC";

      if (!empty($where)) {
        $objects = $patient->loadList($where, $order, $quantity_limited_request, "patients.patient_id", $ljoin);

        // If we have no next match, we won't have to add a DSC segment
        if ($limit_quantity) {
          $next_one = $patient->loadList($where, $order, "$quantity_limited_request,1", "patients.patient_id", $ljoin);
          if (count($next_one) == 0) {
            $limit_quantity = false;
          }
        }
      }
    }
    else {
      $ljoin[100] = "patients ON `patients`.`patient_id` = `sejour`.`patient_id`";

      /** @var $sejour CSejour */
      $sejour = new CSejour();

      if (!empty($where)) {
        $objects = $sejour->loadList($where, null, $quantity_limited_request, "sejour.sejour_id", $ljoin);
      }
    }

    // Save information indicating that we are doing an incremental query
    $last = end($objects);
    if ($last && $limit_quantity) {
      $last->_incremental_query = true;
    }

    return $exchange_hl7v2->setPDRAA($ack, $objects, null, $domains);
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
    if (
        ($PID_5_2 = $this->getDemographicsFields($node, "CPatient", "5.2")) ||
        ($PID_5_2 = $this->getDemographicsFields($node, "CPatient", "5.2.1"))
    ) {
      $PID = array_merge($PID, array("prenom" => $PID_5_2));
    }

    // Maiden name
    if ($PID_6_1_1 = $this->getDemographicsFields($node, "CPatient", "6.1.1")) {
      $PID = array_merge($PID, array("nom_jeune_fille" => $PID_6_1_1));
    }

    // Date of birth
    if ($PID_7_1 = $this->getDemographicsFields($node, "CPatient", "7.1")) {
      $PID = array_merge($PID, array("naissance" => CMbDT::date($PID_7_1)));
    }

    // Sexe
    if ($PID_8 = $this->getDemographicsFields($node, "CPatient", "8")) {
      $PID = array_merge($PID, array("sexe" => CHL7v2TableEntry::mapFrom(1, $PID_8)));
    }

    // Patient Adress
    if ($PID_11_1_1 = $this->getDemographicsFields($node, "CPatient", "11.1.1")) {
      $PID = array_merge($PID, array("adresse" => $PID_11_1_1));
    }

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
      "id_number"         => $this->getDemographicsFields($node, "CPatient", "3.1"),
      "namespace_id"      => $this->getDemographicsFields($node, "CPatient", "3.4.1"),
      "universal_id"      => $this->getDemographicsFields($node, "CPatient", "3.4.2"),
      "universal_id_type" => $this->getDemographicsFields($node, "CPatient", "3.4.3"),
    );

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
      "id_number"         => $this->getDemographicsFields($node, "CPatient", "18.1"),
      "namespace_id"      => $this->getDemographicsFields($node, "CPatient", "18.4.1"),
      "universal_id"      => $this->getDemographicsFields($node, "CPatient", "18.4.2"),
      "universal_id_type" => $this->getDemographicsFields($node, "CPatient", "18.4.3"),
    );

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
   * @return array
   */
  function getRequestSejour(DOMNode $node) {
    $PV1 = array();

    // Patient class
    if (
        ($PV1_2 = $this->getDemographicsFields($node, "CSejour", "2")) ||
        ($PV1_2 = $this->getDemographicsFields($node, "CSejour", "2.1"))
    ) {
      $PV1 = array_merge($PV1, array("type" => CHL7v2TableEntry::mapFrom(4, $PV1_2)));
    }

    return $PV1;
  }

  /**
   * Get others PV1 QPD element
   *
   * @param DOMNode $node QPD element
   *
   * @return array
   */
  function getOtherRequestSejour(DOMNode $node) {
    // Recherche du service
    $service = new CService();
    $ds      = $service->getDS();

    $where_returns = array();
    if ($service_name = $this->getDemographicsFields($node, "CSejour", "3.1")) {
      $service_name = preg_replace("/\*+/", "%", $service_name);
      $where["code"] = $ds->prepare("LIKE %", $service_name);
      $ids = array_unique($service->loadIds($where, null, 100));

      // FIXME prendre les affectations en compte

      $where_returns["sejour.service_id"] = $ds->prepareIn($ids);
    }

    // Praticien
    if (
        ($attending_doctor_name = $this->getDemographicsFields($node, "CSejour", "7.2.1"))||
        ($attending_doctor_name = $this->getDemographicsFields($node, "CSejour", "17.2.1"))
    ) {
      $user = new CUser();
      $attending_doctor_name   = preg_replace("/\*+/", "%", $attending_doctor_name);
      $where["user_last_name"] = $ds->prepare("LIKE %", $attending_doctor_name);
      $ids = array_unique($user->loadIds($where, null, 100));

      $where_returns["sejour.praticien_id"] = $ds->prepareIn($ids);
    }

    // M�decin adressant
    if ($referring_doctor_name = $this->getDemographicsFields($node, "CSejour", "8.2.1")) {
      $medecin = new CMedecin();
      $referring_doctor_name = preg_replace("/\*+/", "%", $referring_doctor_name);
      $where["nom"]          = $ds->prepare("LIKE %", $referring_doctor_name);
      $ids = array_unique($medecin->loadIds($where, null, 100));

      $where_returns["sejour.adresse_par_prat_id"] = $ds->prepareIn($ids);
    }

    return $where_returns;
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
   * Get quantity limited request
   *
   * @param DOMNode $node RCP element
   *
   * @return int
   */
  function getContinuationPointer(DOMNode $node) {
    return $this->queryTextNode("DSC.1", $node);
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
      case "CPatient":
        $seg = "PID";
        break;

      case "CSejour":
        $seg = "PV1";
        break;

      default:
    }

    foreach ($this->queryNodes("QPD.3", $node) as $_QPD_3) {
      if ("@$seg.$field" == $this->queryTextNode("QIP.1", $_QPD_3)) {
        return $this->queryTextNode("QIP.2", $_QPD_3);
      }
    }
  }
}
