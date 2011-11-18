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
  var $event_type    = null;
  var $object        = null;
  var $last_log      = null;
  var $profil        = null;
  var $transaction   = null;
  var $code          = null;
  var $version       = null;
  
  var $message       = null;
  var $msg_hl7       = null;

  var $msg_codes     = array();
  
  var $_receiver     = null;
  var $_sender       = null;
  var $_exchange_ihe = null;  
  
  function __construct() {}
  
  function build($object) {}
  
  function handle() {}
  
  static function getEventClass($event_type, $code) {
    return "CHL7Event".$event_type.$code;
  }
  
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

?>