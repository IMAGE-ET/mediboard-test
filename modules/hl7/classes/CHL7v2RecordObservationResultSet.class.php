<?php /* $Id:$ */

/**
 * Record observation result set, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordObservationResultSet 
 * Record observation result set, message XML
 */

class CHL7v2RecordObservationResultSet extends CHL7v2MessageXML {
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
      $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);
      
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
 
  function handle(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    // Traitement du message des erreurs
    $comment = "";
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $patientPI = CValue::read($data['personIdentifiers'], "PI");
    $venueAN   = CValue::read($data['personIdentifiers'], "AN");

    if (!$patientPI || !$venueAN) {
      return $exchange_ihe->setAckAR($ack, "E007", null, $patient);
    }
   
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    // Patient non retrouvé par son IPP
    if (!$IPP->_id) {
      return $exchange_ihe->setAckAR($ack, "E105", null, $patient);
    }
    $patient->load($IPP->object_id); 
    
    $sejour = new CSejour();
    $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    // Séjour non retrouvé par son NDA
    if (!$NDA->_id) {
      return $exchange_ihe->setAckAR($ack, "E205", null, $sejour);
    }
    $sejour->load($NDA->object_id); 
    
    // Récupération de l'opération courante à la date du relevé
    $first_observation = $data["observations"][0];
    $observation_dt = $this->getObservationDateTime($first_observation["OBR"]);
    $operation = $sejour->getCurrOperation($observation_dt);
    if (!$operation->_id) {
      return $exchange_ihe->setAckAR($ack, "E301", null, $operation);
    }
    
    // Récupération des observations
    foreach ($data["observations"] as $_observation) {
      $result_set                = new CObservationResultSet();
      $result_set->patient_id    = $patient->_id;
      $result_set->context_class = "COperation";
      $result_set->context_id    = $operation->_id;
      $result_set->datetime      = mbDateTime($this->getObservationDateTime($_observation["OBR"]));
      if ($msg = $result_set->store()) {
        return $exchange_ihe->setAckAR($ack, "E302", $msg, $operation);
      }
      
      foreach ($_observation["OBX"] as $_OBX) {
        $result = new CObservationResult();
        $result->observation_result_set_id = $result_set->_id;
        $this->mappingObservationResult($_OBX, $result);
        /* @todo à voir si on envoi un message d'erreur ou si on continu ... */
        if ($msg = $result->store()) {
          return $exchange_ihe->setAckAR($ack, "E303", $msg, $operation);
        }
      }
    }
    
    $codes = array ("I301");
    
    return $exchange_ihe->setAckAA($ack, $codes, null, $patient);
  }
  
  function getObservationDateTime(DOMNode $node) {
    return $this->queryTextNode("OBR.7", $node);
  }
  
  function mappingObservationResult(DOMNode $node, CObservationResult $result) {
    // OBX-3: Observation Identifier
    $this->getObservationIdentifier($node, $result);
    
    // OBX-6: Units
    $this->getUnits($node, $result);
    
    // OBX-5: Observation Value (Varies)
    $this->getObservationValue($node, $result);   
    
    // OBX-11: Observation Result Status
    $this->getObservationResultStatus($node, $result);   
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
  
  function getObservationValue(DOMNode $node, CObservationResult $result) {
    $result->value = $this->queryTextNode("OBX.5", $node);
  }
  
  function getObservationResultStatus(DOMNode $node, CObservationResult $result) {
    $result->status = $this->queryTextNode("OBX.11", $node);
  }   
}
?>