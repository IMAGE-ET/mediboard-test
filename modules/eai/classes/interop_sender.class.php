<?php

/**
 * Interop Sender EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropSender 
 * Interoperability Sender
 */
class CInteropSender extends CInteropActor {
  function updateFormFields() {
    parent::updateFormFields();
       
    $this->_parent_class_name = "CInteropSender";
  }
  
  /**
   * Get child senders
   * 
   * @return array CInteropSender collection 
   */
  static function getChildSenders() {    
    return CApp::getChildClasses("CInteropSender", array(), true);
  }
  
  /**
   * Get objects
   * 
   * @return array CInteropSender collection 
   */
  function getObjects() {
    $objects = array();
    foreach (self::getChildSenders() as $_interop_sender) {
      $itemSender = new $_interop_sender;
      
      // Récupération de la liste des destinataires
      $where = array();
      $objects[$_interop_sender] = $itemSender->loadList($where);
      if (!is_array($objects[$_interop_sender])) {
        continue;
      }
      foreach ($objects[$_interop_sender] as $_sender) {
        $_sender->loadRefGroup();
        $_sender->isReachable();
        $_sender->lastMessage();
      }
    }
    
    return $objects;
  }
  
  function read() {}
}

?>