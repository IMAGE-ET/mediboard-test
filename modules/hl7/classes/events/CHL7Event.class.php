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
 * Class CHL7Event 
 * Event HL7
 */
class CHL7Event {
  /**
   * @var null
   */
  public $event_type;
  /**
   * @var null
   */
  public $object;
  /**
   * @var CUserLog
   */
  public $last_log;
  /**
   * @var string
   */
  public $profil;
  /**
   * @var string
   */
  public $transaction;
  /**
   * @var string
   */
  public $code;
  /**
   * @var string
   */
  public $struct_code;
  /**
   * @var string
   */
  public $version;

  /**
   * @var CHL7v2Message
   */
  public $message;
  /**
   * @var string
   */
  public $msg_hl7;

  /**
   * @var array
   */
  public $msg_codes     = array();

  /**
   * @var CReceiverHL7v2
   */
  public $_receiver;
  
  /**
   * @var CInteropSender
   */
  public $_sender;

  /**
   * @var CExchangeDataFormat
   */
  public $_data_format;

  /**
   * @var CExchangeHL7v2
   */
  public $_exchange_hl7v2;
  /**
   * @var string
   */
  public $_is_i18n;

  /**
   * Construct
   *
   * @param string|null $i18n i18n
   *
   * @return CHL7Event
   */
  function __construct($i18n = null) {
    $this->_is_i18n = $i18n;
  }
  
  /**
   * Build HL7 message
   *
   * @param CMbObject $object Object to use
   *
   * @return void
   */
  function build($object) {
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
   * @return void
   */
  function handle() {
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
}