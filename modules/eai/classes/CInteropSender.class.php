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
  var $user_id   = null;
  
  // Forward references
  var $_ref_user = null;
  
  function updateFormFields() {
    parent::updateFormFields();
       
    $this->_parent_class_name = "CInteropSender";
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["user_id"] = "ref class|CMediusers";

    return $props;
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->uniques["user"] = array ("user_id");
    
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages_supported"]   = "CMessageSupported object_id";
    $backProps["expediteur_generique"] = "CExchangeAny sender_id";
    return $backProps;
  }
  
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", 1);
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
        //$_sender->lastMessage();
      }
    }
    
    return $objects;
  }
  
  function read() {}
  
  function getConfigs(CExchangeDataFormat $data_format) {
    $data_format->getConfigs($this->_guid);
    $format_config = $data_format->_configs_format;
    
    if (!$format_config->_id) {
      return;
    }
    
    foreach ($format_config->getConfigFields() as $_config_field) {
      $this->_configs[$_config_field] = $format_config->$_config_field;
    }
  }
}

?>