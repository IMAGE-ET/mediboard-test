<?php

/**
 * IHE Object Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CIHEObjectHandler 
 * IHE Object Handler
 */
class CIHEDelegatedHandler {
  static $handled     = array ();
  static $profil      = null;
  static $transaction = null;
  
  function sendITI($transaction, $code, CMbObject $mbObject) {
    $receiver  = $mbObject->_receiver;
    
    $class_parent = "CHL7MessageADT$code";
    $event_parent = new $class_parent;
    if (!$receiver->isMessageSupported($event_parent)) {
      return;
    }
    
    $hl7_version = $receiver->getHL7Version($transaction);
    $class       = "CHL7Message".$hl7_version."ADT".$code;
    if (!class_exists($class)) {
      /*@todo trigger_error */
      return;  
    }
    
    $event              = new $class;
    $event->profil      = self::$profil;
    $event->transaction = self::$transaction;
    $receiver->sendEvent($event, $mbObject);
  }
  
}

?>