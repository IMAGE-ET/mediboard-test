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
 * Class CHL7v2ReceiveOrderMessage
 * Order message, message XML HL7
 */
class CHL7v2ReceiveOrderMessage extends CHL7v2MessageXML {
  static $event_codes = "O01";

  /**
   * Get contents
   *
   * @return array
   */
  function getContentNodes() {
    $data = parent::getContentNodes();

    $this->queryNode("PV1", null, $data, true);

    $ORDER = $this->queryNodes("ORM_O01.ORDER", null, $varnull, true);
    foreach ($ORDER as $_ORM_O01_ORDER) {
      // ORC
      $this->queryNode("ORC", $_ORM_O01_ORDER, $data, true);
    }

    $ORDER_DETAIL          = $this->queryNode("ORM_O01.ORDER_DETAIL", null, $varnull, true);
    $ORDER_DETAIL_SEGMENTS = $this->queryNode("ORM_O01.ORDER_DETAIL_SEGMENTS", $ORDER_DETAIL, $varnull, true);

    // OBR
    $this->queryNode("OBR", $ORDER_DETAIL_SEGMENTS, $data, true);

    return $data;
  }

  /**
   * Handle receive order message
   *
   * @param CHL7Acknowledgment $ack        Acknowledgment
   * @param CPatient           $newPatient Person
   * @param array              $data       Data
   *
   * @return string|void
   */
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;



    return $exchange_ihe->setAckAA($ack, null);
  }
}
