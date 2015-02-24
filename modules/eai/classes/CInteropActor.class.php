<?php

/**
 * Interop Actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropActor 
 * Interoperability Actor
 */
class CInteropActor extends CMbObject {
  // DB Fields

  /** @var string */
  public $nom;

  /** @var string */
  public $libelle;

  /** @var string */
  public $group_id;

  /** @var int */
  public $actif;
  
  // Form fields
  /** @var int */
  public $_reachable;

  /** @var string */
  public $_parent_class;

  /** @var bool */
  public $_delete_file = true;

  /** @var string */
  public $_tag_patient;

  /** @var string */
  public $_tag_sejour;

  /** @var string */
  public $_tag_mediuser;

  /** @var string */
  public $_tag_service;

  /** @var string */
  public $_tag_chambre;

  /** @var string */
  public $_tag_lit;

  /** @var string */
  public $_tag_movement;

  /** @var string */
  public $_tag_visit_number;

  /** @var string */
  public $_tag_consultation;

  /** @var string */
  public $_self_tag;

  /** @var array */
  public $_tags = array(); // All tags
  
  // Forward references
  /** @var CGroups */
  public $_ref_group;

  /** @var CExchangeSource[] */
  public $_ref_exchanges_sources;

  /** @var CExchangeDataFormat */
  public $_ref_last_message;

  /** @var CMessageSupported[] */
  public $_ref_messages_supported;

  /** @var array */
  public $_ref_msg_supported_family = array();

  /** @var CEAITransformation[] */
  public $_ref_eai_transformations;

  /** @var CDomain */
  public $_ref_domain;

  /** @var string */
  public $_type_echange;

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"]        = "str notNull";
    $props["libelle"]    = "str";
    $props["group_id"]   = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]      = "bool notNull";
    
    $props["_reachable"]        = "bool";
    $props["_parent_class"]     = "str";

    $props["_self_tag"]         = "str";
    $props["_tag_patient"]      = "str";
    $props["_tag_sejour"]       = "str";
    $props["_tag_consultation"] = "str";
    $props["_tag_mediuser"]     = "str";
    $props["_tag_service"]      = "str";
    $props["_tag_chambre"]      = "str";
    $props["_tag_lit"]          = "str";
    $props["_tag_movement"]     = "str";
    $props["_tag_visit_number"] = "str";
    
    return $props;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
        
    $this->_view = $this->libelle ? $this->libelle : $this->nom;
    $this->_type_echange = $this->_class;

    $this->_self_tag          = $this->getTag($this->group_id);

    $this->_tag_patient       = CPatient::getTagIPP($this->group_id);  
    $this->_tag_sejour        = CSejour::getTagNDA($this->group_id);

    $this->_tag_consultation = CConsultation::getObjectTag($this->group_id);
    $this->_tag_mediuser     = CMediusers::getObjectTag($this->group_id);
    $this->_tag_service      = CService::getObjectTag($this->group_id);
    $this->_tag_chambre      = CChambre::getObjectTag($this->group_id);
    $this->_tag_lit          = CLit::getObjectTag($this->group_id);
    $this->_tag_movement     = CMovement::getObjectTag($this->group_id);
    $this->_tag_visit_number = CSmp::getObjectTag($this->group_id);
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["messages_supported"]    = "CMessageSupported object_id";
    $backProps["domain"]                = "CDomain actor_id";
    $backProps["dicom_exchanges"]       = "CExchangeDicom receiver_id";
    $backProps["routes_receiver"]       = "CEAIRoute receiver_id";
    $backProps["mvsante_exchange"]      = "CExchangeMVSante receiver_id";
    $backProps["actor_transformations"] = "CEAITransformation actor_id";

    return $backProps;
  }

  /**
   * Get actor tags
   *
   * @return array
   */
  function getTags() {
    $tags = array();
    
    foreach ($this->getSpecs() as $key => $spec) {
      if (strpos($key, "_tag_") === false) {
        continue;
      }
      
      $tags[$key] = $this->$key;
    }

    return $this->_tags = $tags;
  }

  /**
   * Get actor tag
   *
   * @param int $group_id Group
   *
   * @return string
   */
  function getTag($group_id = null) {
    // Recherche de l'établissement
    $group = CGroups::get($group_id);
    if (!$group_id) {
      $group_id = $group->_id;
    }

    $cache = new Cache(__METHOD__, array($group_id), Cache::INNER);
    if ($cache->exists()) {
      return $cache->get();
    }

    $ljoin["group_domain"] = "`group_domain`.`domain_id` = `domain`.`domain_id`";

    $where = array();
    $where["group_domain.group_id"] = " = '$group_id'";

    $where["domain.actor_class"]    = " = '$this->_class'";
    $where["domain.actor_id"]       = " = '$this->_id'";

    $domain = new CDomain();
    $domain->loadObject($where, null, null, $ljoin);

    return $cache->put($domain->tag, false);
  }

  /**
   * Get idex
   *
   * @param CMbObject $object Object
   *
   * @return CIdSante400
   */
  function getIdex(CMbObject $object) {
    return CIdSante400::getMatchFor($object, $this->getTag($this->group_id, $this->_class));
  }

  /**
   * Load group forward reference
   *
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }

  /**
   * Load user forward reference
   *
   * @return void
   */
  function loadRefUser() {
  }
  
  /**
   * Get exchanges sources
   * 
   * @return void
   */
  function loadRefsExchangesSources() {
  }

  /**
   * Return the fisrt element of exchangesSources
   *
   * @return mixed|null
   */
  function getFirstExchangesSources() {
    $this->loadRefsExchangesSources();
    if (!$this->_ref_exchanges_sources) {
      return null;
    }

    return reset($this->_ref_exchanges_sources);
  }

  /**
   * Load transformations
   *
   * @param array $where Additional where clauses
   *
   * @return CEAITransformationRule[]
   */
  function loadRefsEAITransformation($where = array()) {
    return $this->_ref_eai_transformations = $this->loadBackRefs("actor_transformations", null, null, null, null, null, null, $where);
  }

  /**
   * Load domain
   *
   * @param array $where Additional where clauses
   *
   * @return CDomain[]
   */
  function loadRefDomain($where = array()) {
    return $this->_ref_domain = $this->loadUniqueBackRef("domain", null, null, null, null, null, null, $where);
  }

  /**
   * Sender is reachable ?
   * 
   * @return boolean reachable
   */
  function isReachable() {
    if (!$this->_ref_exchanges_sources) {
      $this->loadRefsExchangesSources();
    }
  }

  /**
   * Last message
   *
   * @return void
   */
  function lastMessage() {
  }

  /**
   * Register actor ?
   *
   * @param string $name Actor name
   *
   * @return void
   */
  function register($name) {
    $this->nom = $name;
    $this->loadMatchingObject();
    
    // Enregistrement automatique d'un destinataire ?
    //if (!$this->_id) {}
  }

  /**
   * Load messages supported back reference collection
   *
   * @return CStoredObject[]
   */
  function loadRefsMessagesSupported() {
    return $this->_ref_messages_supported = $this->loadBackRefs("messages_supported");
  }

  /**
   * Is that the message is supported by this actor
   *
   * @param string $message Message
   *
   * @return bool
   */
  function isMessageSupported($message) {
    $msg_supported               = new CMessageSupported();
    $msg_supported->object_class = $this->_class;
    $msg_supported->object_id    = $this->_id;
    $msg_supported->message      = $message;
    $msg_supported->active       = 1;

    return $msg_supported->countMatchingList() > 0;
  }

  /**
   * Get messages supported by family
   *
   * @return array
   */
  function getMessagesSupportedByFamily() {    
    $family = array();
    
    foreach (CExchangeDataFormat::getAll() as $_data_format_class) {
      $_data_format = new $_data_format_class;
      $temp = $_data_format->getFamily();
      $family = array_merge($family, $temp);
    }
    
    if (empty($family)) {
      return $this->_ref_msg_supported_family;
    }
    
    $supported = $this->loadRefsMessagesSupported();
    
    foreach ($family as $_family => $_root_class) {
      $root  = new $_root_class;   

      foreach ($root->getEvenements() as $_evt => $_evt_class) {
        foreach ($supported as $_msg_supported) {
          if (!$_msg_supported->active) {
            continue;
          }
          
          if ($_msg_supported->message != $_evt_class) {
            continue;
          }
          
          $messages = $this->_spec->messages;
          if (isset($messages[$root->type])) {
            $this->_ref_msg_supported_family = array_merge($this->_ref_msg_supported_family, $messages[$root->type]);
            continue 3;
          }
        }
      }
    }

    return $this->_ref_msg_supported_family;
  }

  /**
   * Get format object handlers
   *
   * @return array
   */
  function getFormatObjectHandlers() {
    return array();
  }
  
  /**
   * Get objects
   * 
   * @return array CInteropReceiver/CInteropSender collection
   */
  function getObjects() {
    $receiver = new CInteropReceiver();
    $sender   = new CInteropSender(); 
    
    return array(
      "CInteropReceiver" => $receiver->getObjects(),
      "CInteropSender"   => $sender->getObjects()
    );
  }
}