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
  public $user_id;
  public $save_unsupported_message;
  public $create_ack_file;
  public $delete_file;

  // Form fields
  public $_tag_hprimxml;
  public $_tag_phast;
  public $_tag_hl7;
  
  // Forward references
  /** @var CMediusers $_ref_user */
  public $_ref_user;
  /** @var CObjectToInteropSender[] $_ref_object_links */
  public $_ref_object_links;
  /** @var CEAIRoute[] $_ref_routes */
  public $_ref_routes;

  /**
   * @see parent::updateFormFields
   */
  function updateFormFields() {
    parent::updateFormFields();
       
    $this->_parent_class = "CInteropSender";

    if (CModule::getActive("hprimxml")) {
      $this->_tag_hprimxml = CHPrimXML::getObjectTag($this->group_id);
    }

    if (CModule::getActive("phast")) {
      $this->_tag_phast    = CPhast::getTagPhast($this->group_id);
    }

    if (CModule::getActive("hl7")) {
      $this->_tag_hl7      = CHL7::getObjectTag($this->group_id);
    }
  }

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]                  = "ref class|CMediusers";
    $props["save_unsupported_message"] = "bool default|1";
    $props["create_ack_file"]          = "bool default|1";
    $props["delete_file"]              = "bool default|1";

    $props["_tag_hprimxml"] = "str";
    $props["_tag_phast"]    = "str";
    $props["_tag_hl7"]      = "str";
    
    return $props;
  }

  /**
   * @see parent::getSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->uniques["user"] = array ("user_id");
    
    return $spec;
  }

  /**
   * @see parent::getBackProps
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["messages_supported"] = "CMessageSupported object_id";
    $backProps["object_links"]       = "CObjectToInteropSender sender_id";
    $backProps["routes_sender"]      = "CEAIRoute sender_id";

    return $backProps;
  }

  /**
   * Load object links
   *
   * @return CObjectToInteropSender[]
   */
  function loadRefsObjectLinks() {
    if ($this->_ref_object_links) {
      return $this->_ref_object_links;
    }

    return $this->_ref_object_links = $this->loadBackRefs("object_links");
  }

  /**
   * @see parent::loadRefUser
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", 1);
  }

  /**
   * Load routes
   *
   * @param array $where Clause where
   *
   * @return CEAIRoute[]
   */
  function loadRefsRoutes($where = array()) {
    return $this->_ref_routes = $this->loadBackRefs("routes_sender", null, null, null, null, null, null, $where);
  }
  
  /**
   * Get child senders
   * 
   * @return array CInteropSender collection 
   */
  static function getChildSenders() {    
    return CApp::getChildClasses("CInteropSender", true);
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
      $order = "group_id ASC, libelle ASC, nom ASC";
      $objects[$_interop_sender] = $itemSender->loadList($where, $order);
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

  /**
   * Read
   *
   * @return void
   */
  function read() {
  }

  /**
   * Get configs
   *
   * @param CExchangeDataFormat $data_format Exchange
   *
   * @return void
   */
  function getConfigs(CExchangeDataFormat $data_format) {
    $data_format->getConfigs($this->_guid);
    $format_config = $data_format->_configs_format;
    
    if (!isset($format_config->_id)) {
      return;
    }
    
    foreach ($format_config->getConfigFields() as $_config_field) {
      $this->_configs[$_config_field] = $format_config->$_config_field;
    }
  }
}