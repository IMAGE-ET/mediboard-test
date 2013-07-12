<?php

/**
 * Receiver HL7 v.3
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CReceiverHL7v3
 * Receiver HL7 v.3
 */

class CReceiverHL7v3 extends CInteropReceiver {
  // DB Table key

  /** @var null */
  public $receiver_hl7v3_id;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_hl7v3';
    $spec->key   = 'receiver_hl7v3_id';
    $spec->messages = array(
      "PRPA" => array ("CPRPAMessaging"),
    );
    
    return $spec;
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['echanges'] = "CExchangeHL7v3 receiver_id";

    return $backProps;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();

  }

  /**
   * Get object handler
   *
   * @param CEAIObjectHandler $objectHandler Object handler
   *
   * @return mixed
   */
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    return array();
  }

  /**
   * Get event message
   *
   * @param string $profil Profil name
   *
   * @return mixed
   */
  function getEventMessage($profil) {
    if (!array_key_exists($profil, $this->_spec->messages)) {
      return;
    }

    return reset($this->_spec->messages[$profil]);
  }

  /**
   * Send event
   *
   * @param CHL7v3Event $evenement Event type
   * @param CMbObject   $mbObject  Object
   * @param array       $headers   Headers
   * @param boolean     $soapVar   XML message ?
   *
   * @throws Exception
   *
   * @return null|string
   */
  function sendEvent($evenement, CMbObject $mbObject, $headers, $soapVar = false) {
    $evenement->_receiver = $this;

    if (!$this->isMessageSupported(get_class($evenement))) {
      return false;
    }

    $this->loadConfigValues();
    $evenement->build($mbObject);

    $source = CExchangeSource::get("$this->_guid-C{$evenement->event_type}Messaging");

    if (!$source->_id || !$source->active) {
      return null;
    }

    $exchange = $evenement->_exchange_hl7v3;

    $msg = $evenement->message;
    if ($soapVar) {
      $msg = preg_replace("#^<\?xml[^>]*>#", "", $msg);
      $msg = new SoapVar($msg, XSD_ANYXML);
    }

    if ($headers) {
      $source->_headerbody = $headers;
    }

    $source->setData($msg, null, $exchange);
    try {
      $source->send($evenement->_dmp_event_name);
    }
    catch (Exception $e) {
      throw $e;
    }

    $exchange->date_echange = CMbDT::dateTime();

    $ack_data = $source->getACQ();

    if (!$ack_data) {
      $exchange->store();
      return null;
    }

    if (!$ack = CPRPAMessaging::getAcknowledgment($ack_data)) {
      $exchange->store();
      return null;
    }

    $exchange->date_echange        = CMbDT::dateTime();
    $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
    $exchange->acquittement_valide = $ack->dom->schemaValidate() ? 1 : 0;
    $exchange->_acquittement       = $ack_data;
    $exchange->store();

    $ack->object          = $mbObject;
    $ack->_receiver       = $this;
    $ack->_exchange_hl7v3 = $exchange;

    return $ack;
  }
}