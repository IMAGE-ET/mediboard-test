<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
/**
 * Destinataire de messages Hprimsante
 * Class CReceiverHprimSante
 */
class CReceiverHprimSante extends CInteropReceiver {
  // DB Table key
  public $receiver_hprimsante_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_hprimsante';
    $spec->key   = 'receiver_hprimsante_id';
    $spec->messages = array(
      "ADM" => array ("ADM"),
      "REG" => array ("REG"),
      "ORU" => array ("ORU"),
    );

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps['object_configs'] = "CReceiverHPrimSanteConfig object_id";
    $backProps['echanges']       = "CExchangeHprimSante receiver_id";

    return $backProps;
  }

  /**
   * get format of the object handler
   *
   * @param CEAIObjectHandler $objectHandler object handler
   *
   * @return mixed|null
   */
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {

    $hprim_object_handlers = CHPrimSante::getObjectHandlers();
    $object_handler_class  = get_class($objectHandler);

    if (array_key_exists($object_handler_class, $hprim_object_handlers)) {
      return $hprim_object_handlers[$object_handler_class];
    }

    return null;
  }

  /**
   * Send en event
   *
   * @param CHPrimSanteEvent $evenement evenement
   * @param CMbObject        $mbObject  object
   *
   * @return null|String
   * @throws CMbException
   */
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;
    $this->loadConfigValues();
    $evenement->build($mbObject);

    if (!$msg = $evenement->flatten()) {
      return null;
    }

    /** @var CExchangeHprimSante $exchange */
    $exchange = $evenement->_exchange_hpr;

    if (!$exchange->message_valide) {
      return null;
    }

    if (!$this->synchronous) {
      return null;
    }
    $source = CExchangeSource::get("$this->_guid-$evenement->type");

    if (!$source->_id || !$source->active) {
      return null;
    }

    $source->setData($msg, null, $exchange);
    try {
      $source->send();
    }
    catch (Exception $e) {
      throw new CMbException("CExchangeSource-no-response");
    }

    $exchange->date_echange = CMbDT::dateTime();

    $ack_data = $source->getACQ();

    if (!$ack_data) {
      $exchange->store();
      return null;
    }

    /** @var CHPrimSanteEvent $data_format */
    $data_format = CHPrimSante::getEvent($exchange);

    $ack = new CHPrimSanteAcknowledgment($data_format);
    $ack->handle($ack_data);
    $exchange->date_echange        = CMbDT::dateTime();
    $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
    $exchange->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange->_acquittement       = $ack_data;
    $exchange->store();

    return $ack_data;
  }
}