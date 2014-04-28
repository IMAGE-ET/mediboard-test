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
 * Exchanges HprimSante
 */
class CExchangeHprimSante extends CExchangeTabular {
  static $messages = array(
    "ADM" => "CADM",
    "REG" => "CREG",
    "ORU" => "CORU",
  );

  // DB Table key
  public $exchange_hprimsante_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'exchange_hprimsante';
    $spec->key   = 'exchange_hprimsante_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["sender_class"]  = "enum list|CSenderFTP|CSenderSOAP|CSenderMLLP|CSenderFileSystem show|0";
    $props["receiver_id"]   = "ref class|CReceiverHprimSante";

    $props["object_class"]  = "enum list|CPatient|CSejour|CMedecin show|0";

    $props["_message"]      = "hpr";
    $props["_acquittement"] = "hpr";

    return $props;
  }

  /**
   * Get hprim sante config for one actor
   *
   * @param string $actor_guid Actor GUID
   *
   * @return CHPrimSanteConfig|void
   */
  function getConfigs($actor_guid) {
    list($sender_class, $sender_id) = explode("-", $actor_guid);

    $sender_hprimsante_config = new CHPrimSanteConfig();
    $sender_hprimsante_config->sender_class = $sender_class;
    $sender_hprimsante_config->sender_id    = $sender_id;
    $sender_hprimsante_config->loadMatchingObject();

    return $this->_configs_format = $sender_hprimsante_config;
  }

  /**
   * @see parent::handler
   */
  function handle() {
    return COperatorHPrimSante::event($this);
  }

  /**
   * @see parent::getFamily
   */
  function getFamily() {
    return self::$messages;
  }

  /**
   * @see parent::isWellFormed
   */
  function isWellFormed($data, CInteropActor $actor = null) {
    try {
      return CHPrimSanteMessage::isWellFormed($data);
    } catch (Exception $e) {
      return false;
    }
  }

  /**
   * @see parent::understand
   */
  function understand($data, CInteropActor $actor = null) {
    if (!$this->isWellFormed($data, $actor)) {
      return false;
    }

    $hpr_message = $this->parseMessage($data, false, $actor);

    $hpr_message_evt = "CHPrimSante$hpr_message->event_name".$hpr_message->type;

    foreach ($this->getFamily() as $_message) {
      $message_class = new $_message;
      $evenements = $message_class->getEvenements();

      if (in_array($hpr_message_evt, $evenements)) {
        $this->_family_message_class = $_message;
        $this->_family_message       = new $hpr_message_evt;

        return true;
      }
    }
    return false;
  }

  /**
   * @see parent::getMessage
   */
  function getMessage() {
    if ($this->_message !== null) {
      $hpr_message = $this->parseMessage($this->_message);

      $this->_doc_errors_msg   = !$hpr_message->isOK(CHL7v2Error::E_ERROR);
      $this->_doc_warnings_msg = !$hpr_message->isOK(CHL7v2Error::E_WARNING);

      $this->_message_object   = $hpr_message;

      return $hpr_message;
    }
    return null;
  }

  /**
   * @see parent::getACK
   */
  function getACK() {
    if ($this->_acquittement !== null) {
      $hpr_ack = new CHPrimSanteMessage();
      $hpr_ack->parse($this->_acquittement);

      $this->_doc_errors_ack   = !$hpr_ack->isOK(CHL7v2Error::E_ERROR);
      $this->_doc_warnings_ack = !$hpr_ack->isOK(CHL7v2Error::E_WARNING);

      return $hpr_ack;
    }
    return null;
  }

  /**
   * parse message
   *
   * @param String        $string     message
   * @param bool          $parse_body parse the body
   * @param CInteropActor $actor      actor
   *
   * @return CHPrimSanteMessage
   */
  function parseMessage($string, $parse_body = true, $actor = null) {
    $hpr_message = new CHPrimSanteMessage();

    if (!$this->_id && $actor) {
      $this->sender_id    = $actor->_id;
      $this->sender_class = $actor->_class;
    }

    $hpr_message->parse($string, $parse_body);

    return $hpr_message;
  }

  /**
   * populate the exchange
   *
   * @param CExchangeDataFormat $data_format data format
   * @param CHPrimSanteEvent    $event       evenement
   *
   * @return void
   */
  function populateExchange(CExchangeDataFormat $data_format, CHPrimSanteEvent $event) {
    $this->group_id        = $data_format->group_id;
    $this->sender_id       = $data_format->sender_id;
    $this->sender_class    = $data_format->sender_class;
    $this->version         = $event->message->version;
    $this->type            = $event->type_liaison;
    $this->sous_type       = $event->type;
    $this->_message        = $data_format->_message;
  }

  /**
   * populate the Acknowledgment exchange
   *
   * @param CHPrimSanteAcknowledgment $ack      Acknowledgment
   * @param CMbObject                 $mbObject object
   *
   * @return mixed
   */
  function populateExchangeACK(CHPrimSanteAcknowledgment $ack, CMbObject $mbObject = null) {
    $msgAck = $ack->event_err->msg_hpr;

    if ($mbObject && $mbObject->_id) {
      $this->setObjectIdClass($mbObject);
      $this->setIdPermanent($mbObject);
    }

    $this->statut_acquittement = $ack->ack_code ? $ack->ack_code : "ok";
    $this->acquittement_valide = $ack->event_err->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;

    $this->_acquittement = $msgAck;
    $this->date_echange  = CMbDT::dateTime();
    $this->store();

    return $msgAck;
  }

  /**
   * Generate acknowledgment
   *
   * @param CHPrimSanteAcknowledgment $dom_acq Acknowledgment
   * @param CHPrimSanteError[]        $errors  hprim sante errors
   * @param CMbObject                 $object  Object
   *
   * @return CHPrimSanteAcknowledgment
   */
  function setAck(CHPrimSanteAcknowledgment $dom_acq, $errors, CMbObject $object = null) {
    $acq = $dom_acq->generateAcknowledgment($errors, $object);

    return $this->populateExchangeACK($acq, $object);
  }
}
