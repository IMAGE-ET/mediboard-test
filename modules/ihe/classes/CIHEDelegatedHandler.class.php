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
  
  function sendITI($profil, $transaction, $code, CMbObject $mbObject) {
    $receiver    = $mbObject->_receiver;
    
    if (!$receiver->isMessageSupported("CHL7EventADT$code")) {
      return;
    }
    
    $hl7_version = $receiver->getHL7Version($transaction);
    $class       = "CHL7".$hl7_version."EventADT".$code;
    if (!class_exists($class)) {
      /*@todo trigger_error */
      return;  
    }

    $event              = new $class;
    $event->profil      = $profil;
    $event->transaction = $transaction;
    $receiver->sendEvent($event, $mbObject);
  }
  
}

?>