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
  
  function getI18nCode($receiver) {
    $i18n_code = $receiver->_i18n_code;
    if ($i18n_code) {
      $i18n_code = "_$i18n_code";
    }
    
    return $i18n_code;
  }
  
  function isMessageSupported($transaction, $message, $code, $receiver) {
    $i18n_code = $this->getI18nCode($receiver);
    if (!$receiver->isMessageSupported("CHL7Event{$message}{$code}{$i18n_code}")) {
      return false;
    }
    
    return true;
  }
  
  function sendITI($profil, $transaction, $message, $code, CMbObject $mbObject) {
    $receiver = $mbObject->_receiver;

    if (!$code) {
      throw new CMbException("CITI-code-none");
    }

    $i18n_code = $this->getI18nCode($receiver);
    if ($i18n_code) {
      $profil = $profil.$i18n_code;
    }
    
    $hl7_version = $receiver->getHL7Version($transaction);
    $class       = "CHL7".$hl7_version."Event".$message.$code.$i18n_code;
   
    if (!class_exists($class)) {
      trigger_error("class-CHL7".$hl7_version."Event".$message.$code.$i18n_code."-not-found", E_USER_ERROR);
      return;  
    }

    $event              = new $class;
    $event->profil      = $profil;
    $event->transaction = $transaction;
     
    $receiver->sendEvent($event, $mbObject);
  }
  
}

?>