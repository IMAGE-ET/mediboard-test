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

      if (!$operation->_id) {
        /*$this->codes = array(
          "I301",
        );*/
      }

      foreach ($_observation["OBX"] as $_OBX) {
        // OBX.2 : Value type
        $value_type = $this->getOBXValueType($_OBX);

        switch ($value_type) {
          // Reference Pointer to External Report
          case "RP" :
            if (!$this->getReferencePointerToExternalReport($_OBX, $operation)) {
              return $exchange_ihe->setAckAR($ack, $this->codes, null, $operation);
            }

            break;

          // Encapsulated PDF
          case "ED" :
            if (!$this->getEncapsulatedPDF($_OBX, $patient, $operation)) {
              return $exchange_ihe->setAckAR($ack, $this->codes, null, $operation);
            }

            break;

          // Pulse Generator and Lead Observation Results
          case "ST" :  case "CWE" :  case "DTM" :  case "NM" :  case "SN" :
            if (!$operation->_id) {
              return $exchange_ihe->setAckAR($ack, "E301", null, $operation);
            }

            if (!$this->getPulseGeneratorAndLeadObservationResults($_OBX, $patient, $operation)) {
              return $exchange_ihe->setAckAR($ack, $this->codes, null, $operation);
            }

            break;

          // Not supported
          default :
            return $exchange_ihe->setAckAR($ack, "E302", null, $operation);
        }
      }
    }
    
    return $exchange_ihe->setAckAA($ack, $this->codes, $comment, $object);
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

  /**
   * OBX Segment pulse generator and lead observation results
   *
   * @param DOMNode    $OBX       DOM node
   * @param CPatient   $patient   Person
   * @param COperation $operation Opération
   *
   * @return bool
   */
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
      return true;
    }

    $result = new CObservationResult();
    $result->observation_result_set_id = $result_set->_id;
    $this->mappingObservationResult($OBX, $result);

    /* @todo à voir si on envoi un message d'erreur ou si on continu ... */
    if ($msg = $result->store()) {
      $this->codes[] = "E304";
    }

    return true;
  }

  /**
   * OBX Segment with encapsulated PDF
   *
   * @return bool
   */
  function getEncapsulatedPDF() {

  }

  /**
   * OBX Segment with reference pointer to external report
   *
   * @param DOMNode    $OBX       DOM node
   * @param COperation $operation Opération
   *
   * @return bool
   */
  function getReferencePointerToExternalReport(DOMNode $OBX, COperation $operation) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;

    // Chargement de la source associée à l'expéditeur
    /** @var CInteropSender $sender_link */
    $sender_link = reset($sender->loadRefsObjectLinks())->_ref_object;

    // Aucun expéditeur permettant de récupérer les fichiers
    if (!$sender_link->_id) {
      $this->codes[] = "E340";

      return false;
    }

    $authorized_sources = array(
      "CSenderFileSystem",
      "CSenderFTP"
    );

    // L'expéditeur n'est pas prise en charge pour la réception de fichiers
    if (!CMbArray::in($sender_link->_class, $authorized_sources)) {
      $this->codes[] = "E341";

      return false;
    }

    $sender_link->loadRefsExchangesSources();
    // Aucune source permettant de récupérer les fichiers
    if (!$sender_link->_id) {
      $this->codes[] = "E342";

      return false;
    }

    $source = $sender_link->_ref_exchanges_sources[0];

    $filename = $this->getObservationValue($OBX);
    $path     = $filename;

    if ($source instanceof CSourceFileSystem) {
      $path = $source->getFullPath()."/$path";
    }

    $content = $source->getData("$path");

    // Gestion du CFile
    $file = new CFile();
    $file->setObject($operation);
    $file->file_name  = $filename;
    $file->file_type  = "application/pdf";


  }
}
