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
  static $event_codes = array ("O01");

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
   * @param CHL7v2ReceiveOrderMessageResponse $ack     Acknowledgment
   * @param CPatient                          $patient Person
   * @param array                             $data    Data
   *
   * @return string|void
   */
  function handle(CHL7v2ReceiveOrderMessageResponse $ack, CPatient $patient, $data) {
    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender         = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    $patientPI = CValue::read($data['personIdentifiers'], "PI");

    if (!$patientPI) {
      return $exchange_hl7v2->setORRError($ack, "E007");
    }

    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    // Patient non retrouvé par son IPP
    if (!$IPP->_id) {
      return $exchange_hl7v2->setORRError($ack, "E105");
    }
    $patient->load($IPP->object_id);

    $venueAN   = CValue::read($data['personIdentifiers'], "AN");

    $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    // Séjour non retrouvé par son NDA
    if (!$NDA->_id) {
      return $exchange_hl7v2->setORRError($ack, "E205");
    }
    $sejour = new CSejour();
    $sejour->load($NDA->object_id);

    // Common order - ORC
    $event_request = $this->getEventRequest($data["ORC"]);
    switch ($event_request) {
      // new order
      case "NW" :

        break;
      // cancel order request
      case "CA" :

        break;
      default :
        return $exchange_hl7v2->setORRError($ack, "E205");
    }

    return $exchange_hl7v2->setORRSuccess($ack);
  }

  /**
   * Get event request (Order Control)
   *
   * @param DOMNode $node ORC node
   *
   * @return string
   */
  function getEventRequest(DOMNode $node) {
    return $this->queryTextNode("ORC.1", $node);
  }
}
