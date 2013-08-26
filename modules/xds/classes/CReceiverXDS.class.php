<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe receiver XDS
 */
class CReceiverXDS extends CInteropReceiver {

  /** @var integer Primary key */
  public $receiver_xds_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "receiver_xds";
    $spec->key   = "receiver_xds_id";
    $spec->messages = array(
      "producer" => array("producer"),
      "consumer" => array("consumer")
    );
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps             = parent::getBackProps();

    $backProps['echanges'] = "CExchangeXDS receiver_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    return $props;
  }

  /**
   * Send event
   *
   * @param CXDSEventITI41 $evenement Event type
   * @param String         $data      String
   * @param array          $headers   Headers
   *
   * @throws Exception
   *
   * @return null|string
   */
  function sendEvent($evenement, $data, $headers) {

    $source = CExchangeSource::get("$this->_guid-$evenement->evenement_type");

    if (!$source->_id || !$source->active) {
      return null;
    }

    $source->_headerbody = $headers;

    $msg = $evenement->build($data);

    $input = new SoapVar($msg->ownerDocument->saveXML($msg), XSD_ANYXML);

    $source->setData($input);

    try {
      $source->send($evenement->function);
    }
    catch (SoapFault $e) {
      throw $e;
    }

    $ack = $source->getACQ();

    if (!$ack) {
      return "";
    }
    $evenement->ack_data = $ack;
    $ack = $evenement->getAcknowledgment();

    return $ack;
  }
}