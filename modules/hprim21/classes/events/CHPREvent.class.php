<?php

/**
 * Event H'2.1
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPREvent 
 * Event H'2.1
 */
class CHPREvent {
  var $event_type    = null;
  var $object        = null;
  var $last_log      = null;
  var $type          = null;
  var $type_liaison  = null;
  var $version       = null;
  
  var $message       = null;
  var $msg_hpr       = null;

  var $msg_codes     = array();
  
  /**
   * @var CDestinataireHprim21
   */
  var $_receiver     = null;
  
  /**
   * @var CInteropSender
   */
  var $_sender       = null;
  
  var $_exchange_hpr = null; 
  
  /**
   * Build HPR message
   * @param $object Object to use
   */
  function build($object) {}
  
  function handle($msg_hpr) {
    $this->message = new CHPrim21Message();
    
    $this->message->parse($msg_hpr);
    
    return $this->message->toXML(get_class($this), false, CApp::$encoding);
  }
  
  static function getEventClass($event) {
    $classname = "CHPrim21".$event->type.$event->type_liaison;
      
    return $classname;
  }
  
  static function getEvent($message_name) {
    $event_class = "CHPrim21{$message_name}";

    return new $event_class;
  }
}

?>