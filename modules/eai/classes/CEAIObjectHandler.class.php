<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 12588 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CEAIObjectHandler extends CMbObjectHandler {
  static $handled = array ();

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }
  
  function sendFormatAction($action, CMbObject $mbObject) {
    if (!$action) {
      return;
    }
    
    // Parcours des receivers actifs
    $receiver = new CInteropReceiver(); 
    $receivers = $receiver->getObjects();
    foreach ($receivers as $_receivers) {
      if (!$_receivers) {
        continue;
      }
      foreach ($_receivers as $_receiver) { 
        if (!$format_object_handler_classname = $_receiver->getFormatObjectHandler($this)) {
          continue;
        }
        
        $_receiver->loadConfigValues();
        $_receiver->loadRefsMessagesSupported();
        // Affectation du receiver à l'objet
        $mbObject->_receiver = $_receiver;
        
        // Récupère le handler du format
        $format_object_handler = new $format_object_handler_classname;
        // Envoi l'action au handler du format
        $format_object_handler->$action($mbObject);
      }
    }
  }
  
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if (!$mbObject->_ref_last_log) {
      return false;
    }
    
    // Cas d'une fusion
    if ($mbObject->_merging) {
      return false;
    }
    if ($mbObject->_forwardRefMerging) {
      return false;
    }
    
    return true;
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (!$mbObject->_merging) {
      return false;
    }
    
    return true;
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (!$mbObject->_merging) {
      return false;
    }
    
    return true;
  }
  
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }
  
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }
}
?>