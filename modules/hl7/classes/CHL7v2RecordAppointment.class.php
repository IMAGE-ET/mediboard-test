<?php
/**
 * $Id: CHL7v2RecordObservationResultSet.class.php 16357 2012-08-10 08:18:37Z lryo $
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16357 $
 */

/**
 * Class CHL7v2RecordAppointment 
 * Record appointment, message XML
 */
class CHL7v2RecordAppointment extends CHL7v2MessageXML {
  static $event_codes = "S12 S13 S14 S15";
  
  function getContentNodes() {
    $data = $resources = array();
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
    
    $this->queryNode("SCH", null, $data, true);
    
    $PID = $this->queryNode("PID", null, $data, true);
    
    $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);

    $this->queryNode("PD1", null, $data, true);
    
    
    
    
    return $data;
  }
 
  function handle(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    // Traitement du message des erreurs
    $comment = "";
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    mbTrace($data);
  }
}

?>