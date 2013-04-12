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
  /**
   * @var array
   */
  static $handled = array ();
  /**
   * @var null
   */
  public $_eai_initiateur_group_id;


  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
  
  /**
   * Trigger action on the right handler
   * 
   * @param string    $action   Action name
   * @param CMbObject $mbObject Object
   * 
   * @return void
   */
  function sendFormatAction($action, CMbObject $mbObject) {
    if (!$action) {
      return;
    }

    $cn_receiver_guid = CValue::sessionAbs("cn_receiver_guid");

    // Parcours des receivers actifs
    if (!$cn_receiver_guid) {
      $receiver = new CInteropReceiver();
      $receivers = $receiver->getObjects();
    }
    // Sinon envoi destinataire sélectionné
    else {
      if ($cn_receiver_guid == "none") {
        return;
      }
      $receiver = CMbObject::loadFromGuid($cn_receiver_guid);
      if (!$receiver->_id) {
        return;
      }
      $receivers[$receiver->_class][] = $receiver;
    }

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
   * Trigger before event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
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
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
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
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
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
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
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
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }
}