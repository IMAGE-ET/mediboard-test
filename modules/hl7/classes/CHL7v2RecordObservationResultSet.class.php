<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2RecordObservationResultSet 
 * Record observation result set, message XML
 */
class CHL7v2RecordObservationResultSet extends CHL7v2MessageXML {
  static $event_codes = array("R01");

  public $codes = array();

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data = $patient_results = array();
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
    
    $patient_results = $this->queryNodes("ORU_R01.PATIENT_RESULT", null, $varnull, true);
    
    foreach ($patient_results as $_patient_result) {
      // Patient
      $oru_patient = $this->queryNode("ORU_R01.PATIENT", $_patient_result, $varnull);
      $PID = $this->queryNode("PID", $oru_patient, $data, true);
      $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);
      
      // Venue
      $oru_visit = $this->queryNode("ORU_R01.VISIT", $oru_patient, $varnull);
      $PV1 = $this->queryNode("PV1", $oru_visit, $data, true);
      if ($PV1) {
        $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);
      }
      
      // Observations
      $order_observations = $this->queryNodes("ORU_R01.ORDER_OBSERVATION", $_patient_result, $varnull);
      $data["observations"] = array();
      foreach ($order_observations as $_order_observation) {
        $tmp = array();
        // OBR
        $this->queryNode("OBR", $_order_observation, $tmp, true);
        
        // OBXs
        $oru_observations = $this->queryNodes("ORU_R01.OBSERVATION", $_order_observation, $varnull);
        foreach ($oru_observations as $_oru_observation) {
          $this->queryNodes("OBX", $_oru_observation, $tmp, true);
        }
        
        $data["observations"][] = $tmp;
      }
    }
    
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
    // Traitement du message des erreurs
    $comment = "";
    $codes   = array();
    $object  = null;
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;

    $patientPI = CValue::read($data['personIdentifiers'], "PI");
    $venueAN   = CValue::read($data['personIdentifiers'], "AN");

    if (!$patientPI) {
      return $exchange_ihe->setAckAR($ack, "E007", null, $patient);
    }
   
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    // Patient non retrouvé par son IPP
    if (!$IPP->_id) {
      return $exchange_ihe->setAckAR($ack, "E105", null, $patient);
    }
    $patient->load($IPP->object_id);

    // Récupération des observations
    foreach ($data["observations"] as $_observation) {
      // Récupération de la date du relevé
      $observation_dt = $this->getOBRObservationDateTime($_observation["OBR"]);

      $NDA = null;
      if ($venueAN) {
        $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
      }

      // Séjour non retrouvé par son NDA
      if ($NDA && $NDA->_id) {
        /** @var CSejour $sejour */
        $sejour = $NDA->loadTargetObject();
      }
      else {
        $where = array(
          "patient_id" => "= '$patient->_id'",
          "annule"     => "= '0'",
        );
        $sejours = CSejour::loadListForDate(CMbDT::date($observation_dt), $where, null, 1);
        $sejour = reset($sejours);

        if (!$sejour) {
          return $exchange_ihe->setAckAR($ack, "E205", null);
        }
      }

      // Récupération de l'opération courante à la date du relevé
      $operation = $sejour->getCurrOperation($observation_dt);

      $this->codes = array(
        "I301",
      );

      foreach ($_observation["OBX"] as $_OBX) {
        // OBX.2 : Value type
        $value_type = $this->getOBXValueType($_OBX);

        switch ($value_type) {
          // Reference Pointer to External Report
          case "RP" :
            $this->getReferencePointerToExternalReport($_OBX, $operation);

            break;

          // Encapsulated PDF
          case "ED" :
            $this->getEncapsulatedPDF($_OBX, $patient, $operation);

            break;

          // Pulse Generator and Lead Observation Results
          case "ST" :  case "CWE" :  case "DTM" :  case "NM" :  case "SN" :
            if (!$operation->_id) {
              return $exchange_ihe->setAckAR($ack, "E301", null, $operation);
            }

            $this->getPulseGeneratorAndLeadObservationResults($_OBX, $patient, $operation);

            break;

          // Not supported
          default :
            return $exchange_ihe->setAckAR($ack, "E302", null, $operation);
        }
      }
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $object);
  }
  
  function getOBRObservationDateTime(DOMNode $node) {
    return $this->queryTextNode("OBR.7", $node);
  }

  function getOBXValueType(DOMNode $node) {
    return $this->queryTextNode("OBX.2", $node);
  }
  
  function getOBXObservationDateTime(DOMNode $node) {
    return $this->queryTextNode("OBX.14/TS.1", $node);
  }

  function getOBXResultStatus(DOMNode $node) {
    return $this->queryTextNode("OBX.11", $node);
  }
  
  function mappingObservationResult(DOMNode $node, CObservationResult $result) {
    // OBX-3: Observation Identifier
    $this->getObservationIdentifier($node, $result);
    
    // OBX-6: Units
    $this->getUnits($node, $result);
    
    // OBX-5: Observation Value (Varies)
    $result->value = $this->getObservationValue($node);
    
    // OBX-11: Observation Result Status
    $result->status =$this->getObservationResultStatus($node);
  }
  
  function getObservationIdentifier(DOMNode $node, CObservationResult $result) {
    $identifier    = $this->queryTextNode("OBX.3/CE.1", $node);
    $text          = $this->queryTextNode("OBX.3/CE.2", $node);
    $coding_system = $this->queryTextNode("OBX.3/CE.3", $node);
    
    $value_type = new CObservationValueType();
    $result->value_type_id = $value_type->loadMatch($identifier, $coding_system, $text);
  }
  
  function getUnits(DOMNode $node, CObservationResult $result) {
    $identifier    = $this->queryTextNode("OBX.6/CE.1", $node);
    $text          = $this->queryTextNode("OBX.6/CE.2", $node);
    $coding_system = $this->queryTextNode("OBX.6/CE.3", $node);
    
    $unit_type = new CObservationValueUnit();
    $result->unit_id = $unit_type->loadMatch($identifier, $coding_system, $text);
  }
  
  function getObservationValue(DOMNode $node) {
    return $this->queryTextNode("OBX.5", $node);
  }
  
  function getObservationResultStatus(DOMNode $node) {
    return $this->queryTextNode("OBX.11", $node);
  }

  function getPulseGeneratorAndLeadObservationResults(DOMNode $OBX, CPatient $patient, COperation $operation) {
    $result_set = new CObservationResultSet();

    $dateTimeOBX = $this->getOBXObservationDateTime($OBX);
    if ($dateTimeOBX) {
      $result_set->patient_id    = $patient->_id;
      $result_set->context_class = "COperation";
      $result_set->context_id    = $operation->_id;
      $result_set->datetime      = CMbDT::dateTime($dateTimeOBX);
      if ($msg = $result_set->store()) {
        $this->codes[] = "E302";
      }
    }

    // Traiter le cas où ce sont des paramètres sans résultat utilisable
    if ($this->getOBXResultStatus($OBX) === "X") {
      return;
    }

    $result = new CObservationResult();
    $result->observation_result_set_id = $result_set->_id;
    $this->mappingObservationResult($OBX, $result);

    /* @todo à voir si on envoi un message d'erreur ou si on continu ... */
    if ($msg = $result->store()) {
      $this->codes[] = "E304";
    }
  }

  function getEncapsulatedPDF() {

  }

  function getReferencePointerToExternalReport(DOMNode $OBX, COperation $operation) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;

    // Chargement de la source associée à l'expéditeur
    $source = reset($sender->loadRefsObjectLinks());

    $filename = $this->getObservationValue($OBX);
  }
}
