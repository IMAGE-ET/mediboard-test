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
  // Form fields
  public $_type_echange;
  public $_exchanges_sources_save = 0;
  public $OID = null;
  public $synchronous;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->messages = array();
    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["OID"]         = "str";
    $props["synchronous"] = "bool default|1 notNull";
    
    $props["_exchanges_sources_save"] = "num";
    return $props;
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["echanges_any"] = "CExchangeAny receiver_id";
    $backProps["routes"]       = "CEAIRoute receiver_id";
    
    return $backProps;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_parent_class = "CInteropReceiver";
  }
  
  /**
   * Get child receivers
   * 
   * @return array CInteropReceiver collection 
   */
  static function getChildReceivers() {    
    return CApp::getChildClasses("CInteropReceiver", array(), true);
  }
  
  /**
   * Get objects
   * 
   * @return array CInteropReceiver collection 
   */
  function getObjects() {
    $objects = array();
    foreach (self::getChildReceivers() as $_interop_receiver) {
      /** @var CInteropReceiver $receiver */
      $receiver = new $_interop_receiver;
      
      $order = "group_id ASC, nom ASC";
      // Récupération de la liste des destinataires
      $objects[$_interop_receiver] = $receiver->loadList(null, $order);
      if (!is_array($objects[$_interop_receiver])) {
        continue;
      }
      foreach ($objects[$_interop_receiver] as $_receiver) {
        /** @var CInteropReceiver $_receiver */
        $_receiver->loadRefGroup();
        $_receiver->isReachable();
        // Pose des problèmes de performances (SLOW QUERY)
        //$_receiver->lastMessage();
      }
    }
    
    return $objects;
  }

  /**
   * get the receiver by oid
   *
   * @param String $oid oid
   *
   * @return CInteropReceiver[]
   */
  static function getObjectsByOID($oid) {
    $objects = array();
    if (!$oid) {
      return $objects;
    }

    foreach (self::getChildReceivers() as $_interop_receiver) {
      /** @var CInteropReceiver $receiver */
      $receiver = new $_interop_receiver;
      $receiver->OID = $oid;
      $objects = array_merge($objects,  $receiver->loadMatchingList());
    }

    return $objects;
  }

  /**
   * Get objects by events
   *
   * @param array            $events   Events name
   * @param CInteropReceiver $receiver receiver
   *
   * @return array Receivers supported
   */
  static function getObjectsBySupportedEvents($events = array(), $receiver = null) {
    $receivers = array();
    foreach ($events as $_event) {
      $msg_supported          = new CMessageSupported();
      $msg_supported->message = $_event;
      $msg_supported->active  = 1;

      if ($receiver && $receiver->_id) {
        $msg_supported->setObject($receiver);
        if (!$msg_supported->loadMatchingObject()) {
          $receivers[$_event] = null;
          return $receivers;
        }
        $receivers[$_event] = $receiver;
        return $receivers;
      }

      $messages = $msg_supported->loadMatchingList(null, null, "object_class");

      foreach ($messages as $_message) {
        /** @var CInteropReceiver $receiver */
        $receiver = CMbObject::loadFromGuid("$_message->object_class-$_message->object_id");
        if (!$receiver->actif) {
          continue;
        }

        $receiver->loadRefGroup();
        $receiver->isReachable();

        $receivers[$_event][] = $receiver;
      }
    }

    return $receivers;
  }

  /**
   * Load exchanges sources
   *
   * @return void
   */
  function loadRefsExchangesSources() {
    if (!$this->_ref_msg_supported_family) {
      $this->getMessagesSupportedByFamily();
    }

    $this->_ref_exchanges_sources = array();
    foreach ($this->_ref_msg_supported_family as $_evenement) {
      $this->_ref_exchanges_sources[$_evenement] = CExchangeSource::get("$this->_guid-$_evenement", null, true, $this->_type_echange);
    }

    return $this->_ref_exchanges_sources;
  }


  /**
   * Get object handler
   *
   * @param CEAIObjectHandler $objectHandler Object handler
   *
   * @return mixed
   */
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    return array();
  }
}