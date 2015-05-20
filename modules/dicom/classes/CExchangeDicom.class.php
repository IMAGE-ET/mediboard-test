<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * A Dicom exchange
 */
class CExchangeDicom extends CExchangeBinary {

  static $messages = array(
    "Echo" => "CEcho",
    "Find" => "CFind",
  );

  /**
   * Table Key
   *
   * @var integer
   */
  public $exchange_dicom_id;

  /**
   * The request
   * If there is several messages, they are separated by "|"
   * 
   * @var string
   */
  public $requests;

  /**
   * The response
   * If there is several messages, they are separated by "|"
   * 
   * @var string
   */
  public $responses;

  /**
   * The presentation contexts, in string
   * 
   * @var string
   */
  public $presentation_contexts;

  /**
   * The request
   * 
   * @var CDicomPDU[]
   */
  public $_requests;

  /**
   * The response
   * 
   * @var CDicomPDU[]
   */
  public $_responses;

  /**
   * The presentation contexts
   * 
   * @var CDicomPresentationContext[]
   */
  public $_presentation_contexts;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table  = "dicom_exchange";
    $spec->key    = "dicom_exchange_id";
    return $spec;
  }

  /**
   * Get the properties of our class as string
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["requests"]              = "text";
    $props["responses"]             = "text";
    $props["presentation_contexts"] = "str show|0";
    //$props["receiver_id"]         = "ref class|CDicomReceiver";
    $props["sender_class"]          = "enum list|CDicomSender show|0";
    $props["object_class"]          = "str class show|0";
    return $props;
  }

  /**
   * Get backward reference specifications
   * 
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["dicom_session"]   = "CDicomSession dicom_exchange_id";

    return $backProps;
  }

  /**
   * Decode the messages
   * 
   * @return null
   */
  function decodeContent() {
    if ($this->presentation_contexts && !$this->_presentation_contexts) {
      $pres_contexts_array = explode('|', $this->presentation_contexts);
      $this->_presentation_contexts = array();

      foreach ($pres_contexts_array as $_pres_context) {
        $_pres_context = explode('/', $_pres_context);

        if (array_key_exists(0, $_pres_context) && array_key_exists(1, $_pres_context) && array_key_exists(2, $_pres_context)) {
          $this->_presentation_contexts[] = new CDicomPresentationContext($_pres_context[0], $_pres_context[1], $_pres_context[2]);
        }
      }
    }

    if ($this->requests && !$this->_requests && $this->_presentation_contexts) {
      $request_msgs = explode("|", $this->requests);
      $this->_requests = array();

      foreach ($request_msgs as $msg) {
        $msg = base64_decode($msg);
        $pdu = CDicomPDUFactory::decodePDU($msg, $this->_presentation_contexts);
        $this->_requests[] = $pdu;
      }
    }

    if ($this->responses && !$this->_responses && $this->_presentation_contexts) {
      $response_msgs = explode("|", $this->responses);
      $this->_responses = array();

      foreach ($response_msgs as $msg) {
        $pdu = CDicomPDUFactory::decodePDU(base64_decode($msg), $this->_presentation_contexts);
        $this->_responses[] = $pdu;
      }
    }
  }

  /**
   * Update the fields tored in database
   * 
   * @return null
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->_presentation_contexts && !$this->presentation_contexts) {
      foreach ($this->_presentation_contexts as $_pres_context) {
        if (!$this->presentation_contexts) {
          $this->presentation_contexts = "$_pres_context->id/$_pres_context->abstract_syntax/$_pres_context->transfer_syntax";
        }
        else {
          $this->presentation_contexts .= "|$_pres_context->id/$_pres_context->abstract_syntax/$_pres_context->transfer_syntax";
        }
      }
    }

    if ($this->_requests) {
      $this->requests = null;
      foreach ($this->_requests as $_request) {
        if (!$this->requests) {
          $this->requests = base64_encode($_request->getPacket());
        }
        else {
          $this->requests .= "|" . base64_encode($_request->getPacket());
        }  
      }
    }

    if ($this->_responses) {
      $this->responses = null;
      foreach ($this->_responses as $_response) {
        if (!$this->responses) {
          $this->responses = base64_encode($_response->getPacket());
        }
        else {
          $this->responses .= "|" . base64_encode($_response->getPacket());
        }  
      }
    }
  }

  /**
   * Handle the message
   * 
   * @return array
   */
  function handle() {
    return COperatorDicom::event($this);
  }

  /**
   * Return the family
   * 
   * @return array
   */
  function getFamily() {
    return self::$messages;
  }

  /**
   * Check if the message is well formed
   * 
   * @param string        $msg   The message
   * 
   * @param CInteropActor $actor The actor who sent the message
   * 
   * @return boolean
   */
  function isWellFormed($msg, CInteropActor $actor = null) {
    $stream = fopen("php://temp", 'w+');
    fwrite($stream, $msg);

    $stream_reader = new CDicomStreamReader($stream);
    $stream_reader->rewind();
    $type = $stream_reader->readHexByte();

    if ($type != "04") {
      $stream_reader->close();
      return false;
    }

    $stream_reader->skip(1);
    $length = $stream_reader->readUInt32();
    $stream_reader->close();

    if (strlen($msg) != $length + 6) {
      return false;
    }

    return true;
  }

  /**
   * Check if we can understand the message
   * 
   * @param string        $msg           The message
   * 
   * @param CInteropActor $actor         The actor
   * 
   * @param array         $pres_contexts The presentation contexts
   * 
   * @return boolean
   */
  function understand($msg , CInteropActor $actor = null, $pres_contexts = null) {
    $this->_presentation_contexts = $pres_contexts;  
    if (!$this->isWellFormed($msg)) {
      return false;
    }

    $pdu = CDicomPDUFactory::decodePDU($msg, $this->_presentation_contexts);
    $pdvs = $pdu->getPDVs();

    $msg_types = array();
    $msg_classes = array();

    foreach ($pdvs as $pdv) {
      $msg = $pdv->getMessage();
      $msg_types[] = $msg->type;
      $msg_classes[] = get_class($msg);
    }


    if ($msg_types[0] == "C-Find-RQ" || $msg_types[0] == "C-Echo-RQ") {
      if (!$this->_requests) {
        $this->_requests = array();
      }
      $this->_requests[] = $pdu;
    }
    elseif ($msg_types[0] == "C-Echo-RSP" || $msg_types[0] == "C-Find-RSP") {
      if (!$this->_responses) {
        $this->_responses = array();
      }
      $this->_responses[] = $pdu;
    }
    elseif ($msg_types[0] == "Datas") {
      if ($this->_responses) {
        $this->_responses[] = $pdu;
      }
      else {
        $this->_requests[] = $pdu;
      }
    }

    foreach ($this->getFamily() as $_family) {
      $family_class = new $_family;
      $events = $family_class->getEvenements();
      if (array_key_exists($msg_types[0], $events)) {
        $this->_family_message_class = $_family;
        $this->_family_message = $msg_classes[0];
        $this->message_valide = 1;
        return true;
      }
    }
    return false;
  }

  /**
   * Get the Dicom configs for the given actor
   *
   * @param string $actor_guid Actor GUID
   *
   * @return CDicomConfig|void
   */
  function getConfigs($actor_guid = null) {
    if ($actor_guid) {
      list($sender_class, $sender_id) = explode('-', $actor_guid);
    }
    else {
      $sender_class = $this->sender_class;
      $sender_id = $this->sender_id;
    }

    $sender_dicom_config               = new CDicomConfig();
    $sender_dicom_config->sender_class = $sender_class;
    $sender_dicom_config->sender_id    = $sender_id;
    $sender_dicom_config->loadMatchingObject();

    return $this->_configs_format = $sender_dicom_config;
  }
}