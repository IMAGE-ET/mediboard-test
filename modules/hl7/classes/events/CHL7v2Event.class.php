<?php

/**
 * Event HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2Event 
 * Event HL7
 */
class CHL7v2Event extends CHL7Event {
  /** @var CExchangeHL7v2 */
  public $_exchange_hl7v2;

  /** @var string */
  public $_is_i18n;

  /** @var string */
  public $profil;

  /** @var string */
  public $transaction;

  /** @var string */
  public $code;

  /** @var string */
  public $struct_code;

  /** @var string */
  public $msg_hl7;

  /** @var array */
  public $msg_codes = array();

  /**
   * Construct
   *
   * @param string|null $i18n i18n
   *
   * @return CHL7v2Event
   */
  function __construct($i18n = null) {
    parent::__construct();

    $this->_is_i18n = $i18n;
  }

  /**
   * Get segment terminator
   *
   * @param string $st The key of the value to get
   *
   * @return mixed
   */
  private function getSegmentTerminator($st) {
    $terminators = array(
      "CR"   => "\r",
      "LF"   => "\n",
      "CRLF" => "\r\n",
    );
    
    return CValue::read($terminators, $st, CHL7v2Message::DEFAULT_SEGMENT_TERMINATOR);
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    // Traitement sur le mbObject
    $this->object   = $object;
    $this->last_log = $object->loadLastLog();
    
    // Récupération de la version HL7 en fonction du receiver et de la transaction
    $this->version  = $this->_receiver->_configs[$this->transaction."_HL7_version"];
    
    // Génération de l'échange
    $this->generateExchange();
 
    $terminator = $this->getSegmentTerminator($this->_receiver->_configs["ER7_segment_terminator"]);
    
    // Création du message HL7
    $message = new CHL7v2Message($this->version);
    $message->segmentTerminator = $terminator;
    $message->name              = $this->msg_codes;
   
    $this->message = $message;
  }

  /**
   * Build specifics HL7 message (i18n)
   *
   * @param CMbObject $object Object to use
   *
   * @return void
   */
  function buildI18nSegments($object) {
  }

  /**
   * Handle event
   *
   * @param string $msg_hl7 HL7 message
   *
   * @return DOMDocument|void
   */
  function handle($msg_hl7) {
    $this->message = new CHL7v2Message();

    if ($this->_data_format) {
      $strict = $this->_data_format->_configs_format->strict_segment_terminator;
      $this->message->strict_segment_terminator = $strict;
      
      if ($strict) {
        $terminator = $this->getSegmentTerminator($this->_data_format->_configs_format->segment_terminator);
        $this->message->segmentTerminator = $terminator;
      }
    }

    $this->message->parse($msg_hl7);

    return $this->message->toXML(get_class($this), false, CApp::$encoding);
  }

  /**
   * Get event class
   *
   * @param CHL7Event $event Event HL7
   *
   * @return string
   */
  static function getEventClass($event) {
    $classname = "CHL7Event".$event->event_type.$event->code;
    if ($event->message->i18n_code) {
      $classname .= "_".$event->message->i18n_code;
    }

    return $classname;
  }

  /**
   * Get event version
   *
   * @param string $version      Version
   * @param string $message_name Message name
   *
   * @return mixed
   * @throws CHL7v2Exception
   */
  static function getEventVersion($version, $message_name) {
    $hl7_version = null;
    foreach (CHL7::$versions as $_version => $_sub_versions) {
      if (in_array($version, $_sub_versions)) {
        $hl7_version = $_version;
      }
    }

    if (!$hl7_version) {
      throw new CHL7v2Exception(CHL7v2Exception::VERSION_UNKNOWN, $version);
    }

    $event_class = "CHL7{$hl7_version}Event{$message_name}";

    return new $event_class;
  }

  /**
   * Get the message as a string
   *
   * @return string
   */
  function flatten() {
    $this->msg_hl7 = $this->message->flatten();
    $this->message->validate();
    
    $this->updateExchange();
    
    return $this->msg_hl7;
  }

  /**
   * Generate exchange HL7v2
   *
   * @return CExchangeHL7v2
   */
  function generateExchange() {
    $exchange_hl7v2                  = new CExchangeHL7v2();
    $exchange_hl7v2->date_production = CMbDT::dateTime();
    $exchange_hl7v2->receiver_id     = $this->_receiver->_id;
    $exchange_hl7v2->group_id        = $this->_receiver->group_id;
    $exchange_hl7v2->sender_id       = $this->_sender ? $this->_sender->_id : null;
    $exchange_hl7v2->sender_class    = $this->_sender ? $this->_sender->_id : null;
    $exchange_hl7v2->version         = $this->version;
    $exchange_hl7v2->type            = $this->profil;
    $exchange_hl7v2->sous_type       = $this->transaction;
    $exchange_hl7v2->code            = $this->code;
    $exchange_hl7v2->object_id       = $this->object->_id;
    $exchange_hl7v2->object_class    = $this->object->_class;
    $exchange_hl7v2->store();

    return $this->_exchange_hl7v2 = $exchange_hl7v2;
  }

  /**
   * Update exchange HL7v2 with
   *
   * @return CExchangeHL7v2
   */
  function updateExchange() {
    $exchange_hl7v2                 = $this->_exchange_hl7v2;
    $exchange_hl7v2->_message       = $this->msg_hl7;
    $exchange_hl7v2->message_valide = $this->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange_hl7v2->store();
    
    return $exchange_hl7v2;
  }
}