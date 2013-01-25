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
  var $event_type    = null;
  /**
   * @var null
   */
  var $object        = null;
  /**
   * @var CUserLog
   */
  var $last_log      = null;
  /**
   * @var string
   */
  var $profil        = null;
  /**
   * @var string
   */
  var $transaction   = null;
  /**
   * @var string
   */
  var $code          = null;
  /**
   * @var string
   */
  var $struct_code   = null;
  /**
   * @var string
   */
  var $version       = null;

  /**
   * @var CHL7v2Message
   */
  var $message       = null;
  /**
   * @var string
   */
  var $msg_hl7       = null;

  /**
   * @var array
   */
  var $msg_codes     = array();
  
  /**
   * @var CReceiverIHE
   */
  var $_receiver     = null;
  
  /**
   * @var CInteropSender
   */
  var $_sender       = null;

  /**
   * @var CExchangeDataFormat
   */
  var $_data_format = null;

  /**
   * @var CExchangeIHE
   */
  var $_exchange_ihe = null;
  /**
   * @var string
   */
  var $_is_i18n      = null;

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