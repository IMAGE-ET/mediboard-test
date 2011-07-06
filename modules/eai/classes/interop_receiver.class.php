<?php

/**
 * Interop Receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropReceiver 
 * Interoperability Receiver
 */
class CInteropReceiver extends CInteropActor {
  var $message     = null;
  
  // Form fields
  var $_type_echange = null;
  var $_exchanges_sources_save = 0;
  
  function getSpec() {
    $spec = parent::getSpec();

    $spec->messages = array();
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["message"]       = "str";

    $props["_exchanges_sources_save"] = "num";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["destinataire_generique"] = "CExchangeAny receiver_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_parent_class_name = "CInteropReceiver";
  }
  
  /**
   * Get child receivers
   * 
   * @return array CInteropReceiver collection 
   */
  static function getChildReceivers() {    
    return CApp::getChildClasses("CInteropReceiver", array(), true);
  }
  
  function register($name) {
    $this->nom = $name;
    $this->loadMatchingObject();
    
    // Enregistrement automatique d'un destinataire ?
    if (!$this->_id) {}
  }
  
  /**
   * Get objects
   * 
   * @return array CInteropReceiver collection 
   */
  function getObjects() {
    $objects = array();
    foreach (self::getChildReceivers() as $_interop_receiver) {
      $receiver = new $_interop_receiver;
      
      $order = "group_id ASC";
      // Récupération de la liste des destinataires
      $objects[$_interop_receiver] = $receiver->loadList(null, $order);
      if (!is_array($objects[$_interop_receiver])) {
        continue;
      }
      foreach ($objects[$_interop_receiver] as $_receiver) {
        $_receiver->loadRefGroup();
        $_receiver->isReachable();
        // Pose des problèmes de performances (SLOW QUERY)
        //$_receiver->lastMessage();
      }
    }
    
    return $objects;
  } 
}

?>
