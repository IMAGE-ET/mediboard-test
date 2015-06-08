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
  public $monitor_sources;

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

    $props["OID"]             = "str";
    $props["synchronous"]     = "bool default|1 notNull";
    $props["monitor_sources"] = "bool default|1 notNull";
    
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
    $backProps["echanges_any"]    = "CExchangeAny receiver_id";

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
    return CApp::getChildClasses("CInteropReceiver", true);
  }
  
  /**
   * Get objects
   *
   * @param bool $actif    Actif
   * @param int  $no_group Group excluded
   * 
   * @return array CInteropReceiver collection 
   */
  function getObjects($actif = false, $no_group = null) {
    $objects = array();
    foreach (self::getChildReceivers() as $_interop_receiver) {
      /** @var CInteropReceiver $receiver */
      $receiver = new $_interop_receiver;

      $where = array();
      if ($actif) {
        $where["actif"]    = " = '1'";
      }

      if ($no_group) {
        $where["group_id"] = " != '$no_group'";
      }
      
      $order = "group_id ASC, libelle ASC, nom ASC";
      // Récupération de la liste des destinataires
      $objects[$_interop_receiver] = $receiver->loadList($where, $order);
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
  static function getObjectsBySupportedEvents($events = array(), CInteropReceiver $receiver = null) {
    $receivers = array();
    foreach ($events as $_event) {
      $msg_supported = new CMessageSupported();

      $where = array();
      $where["message"] = " = '$_event'";
      $where["active"]  = " = '1'";

      $ljoin = array();
      if ($receiver) {
        $table = $receiver->_spec->table;
        $ljoin[$table] = "$table.$table"."_id"." = message_supported.object_id";

        $where["object_class"] = " = '$receiver->_class'";
        if ($receiver->_id) {
          $where["object_id"]  = " = '$receiver->_id'";
        }

        $where["$table.actif"] = " = '1'";
      }

      if (!$msg_supported->loadObject($where, null, null, $ljoin)) {
        $receivers[$_event] = null;
        return $receivers;
      }

      $messages = $msg_supported->loadList($where, "object_class", null, null, $ljoin);

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
   * @return CExchangeSource[]
   */
  function loadRefsExchangesSources() {
    if (!$this->_ref_msg_supported_family) {
      $this->getMessagesSupportedByFamily();
    }

    $this->_ref_exchanges_sources = array();
    foreach ($this->_ref_msg_supported_family as $_evenement) {
      $source = CExchangeSource::get("$this->_guid-$_evenement", null, true, $this->_type_echange);
      if ($source instanceof CSourcePOP) {
        $source->loadRefMetaObject();
      }

      $this->_ref_exchanges_sources[$_evenement] = $source;
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