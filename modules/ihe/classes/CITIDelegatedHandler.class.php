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
 * Class CITIDelegatedHandler 
 * ITI Object Handler
 */
class CITIDelegatedHandler {
  static $handled     = array ();
  
  function sendITI($profil, $transaction, $code, CMbObject $mbObject) {
    $receiver = $mbObject->_receiver;

    if (!$code) {
      throw new CMbException("CITI-code-none");
    }
    
    $extension = null;
    if ($receiver->_configs["extension"]) {
      $extension = "_{$receiver->_configs["extension"]}";
    }

    if (!$receiver->isMessageSupported("CHL7EventADT{$code}{$extension}")) {
      return;
    }
    
    $hl7_version = $receiver->getHL7Version($transaction);
    $class       = "CHL7".$hl7_version."EventADT".$code.$extension;
    if (!class_exists($class)) {
      trigger_error("class-CHL7".$hl7_version."EventADT".$code.$extension."-not-found", UI_MSG_ERROR);
      return;  
    }
    
    if ($extension) {
      $profil = $profil.$extension;
    }

    $event              = new $class;
    $event->profil      = $profil;
    $event->transaction = $transaction;
    $event->extension   = $receiver->_configs["extension"];

    $receiver->sendEvent($event, $mbObject);
  }
  
}

?>