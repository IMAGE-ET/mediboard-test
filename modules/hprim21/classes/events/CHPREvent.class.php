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
  public $event_type;
  public $object;
  public $last_log;
  public $type;
  public $type_liaison;
  public $version;
  
  /**
   * @var CHPrim21Message
   */
  public $message;
  
  public $msg_hpr;

  var $msg_codes     = array();
  
  /**
   * @var CDestinataireHprim21
   */
  public $_receiver;
  
  /**
   * @var CInteropSender
   */
  public $_sender;
  
  public $_exchange_hpr;
  
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

