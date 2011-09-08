<?php

/**
 * ITI30 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI30DelegatedHandler 
 * ITI30 Delegated Handler
 */
class CITI30DelegatedHandler extends CIHEDelegatedHandler {
  static $handled     = array ("CPatient");
  static $profil      = "PAM";
  static $transaction = "ITI30";
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    if (!$mbObject->_IPP) {
      $IPP = new CIdSante400();
      $IPP->loadLatestFor($mbObject, $receiver->_tag_patient);
      
      $mbObject->_IPP = $IPP->id400;
    }

    // Envoi pas les patients qui n'ont pas d'IPP
    if (!$receiver->_configs["send_all_patients"] && !$mbObject->_IPP) {
      return;
    }
    
    switch ($mbObject->loadLastLog()->type) {
      case "create":
        $code = "A28";
        break;
      case "store":
        $code = "A31";
        break;
      default:
        $code = null;
        break;
    }

    if (!$code) {
      return;
    }
    
    $this->sendITI($code, $mbObject);
    
    $mbObject->_IPP = null;
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    
  }  
}
?>