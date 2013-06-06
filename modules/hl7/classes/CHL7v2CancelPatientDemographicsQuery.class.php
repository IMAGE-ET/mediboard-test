<?php
/**
 * Cancel Query
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 18158 $
 */

/**
 * Class CHL7v2CancelPatientDemographicsQuery
 * Cancel Query, message XML HL7
 */
class CHL7v2CancelPatientDemographicsQuery extends CHL7v2MessageXML {
  /**
   * @var string
   */
  static $event_codes = array ("J01");

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data  = array();

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
    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender         = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    return $exchange_hl7v2->setAckAA($ack, null);
  }
}
