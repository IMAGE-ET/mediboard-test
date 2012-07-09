<?php

/**
 * EAI Object handler
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIObjectHandler
 * EAI Object handler
 */

class CEAIObjectHandler extends CMbObjectHandler {
  static $handled               = array ();
  var $_eai_initiateur_group_id = null;
  
  /**
   * @see parent::isHandled()
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
  
  /**
   * Trigger action on the right handler
   * 
   * @param $action   string    Action name
   * @param $mbObject CMbObject Object
   * 
   * @return void
   */
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
        // Destinataire non actif on envoi pas
        if (!$_receiver->actif) {
          continue;
        }
        
        // Affectation du receiver à l'objet
        $mbObject->_receiver = $_receiver;
        
        // Récupère le handler du format
        $format_object_handler = new $format_object_handler_classname;
        // Envoi l'action au handler du format
        try {
          $format_object_handler->$action($mbObject);
        } 
        catch (Exception $e) {
          CAppUI::setMsg($e->getMessage(), UI_MSG_ERROR);
        }
      }
    }
  }
  
  /**
   * @see parent::onBeforeStore()
   */
  function onBeforeStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (isset($mbObject->_eai_initiateur_group_id)) {
      $this->_eai_initiateur_group_id = $mbObject->_eai_initiateur_group_id;
    }
  }
  
  /**
   * @see parent::onAfterStore()
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $mbObject->_eai_initiateur_group_id = $this->_eai_initiateur_group_id;

    if (!$mbObject->_ref_last_log && $mbObject->_class != "CIdSante400") {
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
  
  /**
   * @see parent::onBeforeMerge()
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (!$mbObject->_merging) {
      return false;
    }
    
    return true;
  }
  
  /**
   * @see parent::onAfterMerge()
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (!$mbObject->_merging) {
      return false;
    }
    
    return true;
  }
  
  /**
   * @see parent::onBeforeDelete()
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }
  
  /**
   * @see parent::onAfterDelete()
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }
}
?>