<?php
/**
 * Receive patient demographics response
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2ReceivePatientDemographicsResponse
 * Receive patient demographics response, message XML HL7
 */
class CHL7v2ReceivePatientDemographicsResponse extends CHL7v2MessageXML {
  /**
   * @var string
   */
  static $event_codes = "K22";

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data  = array();

    $this->queryNode("QAK", null, $data, true);

    $this->queryNode("QPD", null, $data, true);

    $query_response = $this->queryNodes("RSP_K22.QUERY_RESPONSE", null, $varnull, true);
    foreach ($query_response as $_query_response) {
      // Patient
      $this->queryNodes("PID", $_query_response, $data, true);
    }

    return $data;
  }

  /**
   * Handle event
   *
   * @return null|string
   */
  function handle() {
    $data = $this->getContentNodes();

    $response_status = $this->getQueryResponseStatus($data["QAK"]);

    // Aucun résultat ou en erreur
    if ($response_status != "OK" || !array_key_exists("PID", $data)) {
      return array();
    }

    $patients = array();

    $recordPerson = new CHL7v2RecordPerson();
    foreach ($data["PID"] as $_PID) {
      $patient = new CPatient();

      $recordPerson->getPID($_PID, $patient);

      $patient->updateFormFields();
      $patient->loadRefsNotes();

      $patients[$this->queryTextNode("PID.1", $_PID)] = $patient;
    }

    return $patients;
  }


  /**
   * Get query response status
   *
   * @param DOMNode $node QAK element
   *
   * @return string
   */
  function getQueryResponseStatus(DOMNode $node) {
    return $this->queryTextNode("QAK.2", $node);
  }
}
