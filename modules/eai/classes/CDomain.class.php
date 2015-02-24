<?php

/**
 * Identification domain
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CDomain
 * Identification domain
 */

class CDomain extends CMbObject {
  // DB Table key
  public $domain_id;
  
  // DB fields
  public $incrementer_id;
  public $actor_id;
  public $actor_class;
  public $tag;
  public $libelle;
  public $namespace_id;
  public $derived_from_idex;
  public $OID;
  public $active;
  
  // Form fields 
  public $_is_master_ipp;
  public $_is_master_nda;
  public $_count_objects;
  public $_detail_objects = array();
  public $_force_merge    = false;

  /** @var CInteropActor */
  public $_ref_actor; 

  /** @var CIncrementer */
  public $_ref_incrementer;

  /** @var CGroupDomain[] */
  public $_ref_group_domains;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'domain';
    $spec->key   = 'domain_id';

    $spec->uniques["actor"] = array ("actor_id", "actor_class", "active");

    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @see parent::getProps()
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["incrementer_id"]    = "ref class|CIncrementer nullify";
    $props["actor_id"]          = "ref class|CInteropActor meta|actor_class nullify";
    $props["actor_class"]       = "str maxLength|80";
    $props["tag"]               = "str notNull";
    $props["libelle"]           = "str";
    $props["namespace_id"]      = "str";
    $props["derived_from_idex"] = "bool";
    $props["OID"]               = "str";
    $props["active"]            = "bool default|1";
    
    $props["_is_master_ipp"] = "bool";
    $props["_is_master_nda"] = "bool";
    $props["_count_objects"] = "num";

    return $props;
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["group_domains"] = "CGroupDomain domain_id";
    
    return $backProps;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // On passe tous les domaines du groupe en "non master"
    if ($this->fieldModified("active", 0)) {
      foreach ($this->loadRefsGroupDomains() as $_group_domain) {
        $_group_domain->master = "0";
        $_group_domain->store();
      }
    }

    return parent::store();
  }
  
  /**
   * Load actor
   *
   * @return CInteropActor
   */
  function loadRefActor() {
    if ($actor = $this->loadFwdRef("actor_id", true)) {
      return $this->_ref_actor = $actor;
    }
    
    return $this->_ref_actor = new CInteropActor();
  }
  
  /**
   * Load incrementer
   *
   * @return CIncrementer
   */
  function loadRefIncrementer() {
    if ($this->_ref_incrementer) {
      return $this->_ref_incrementer;
    }
    
    return $this->_ref_incrementer = $this->loadFwdRef("incrementer_id", true);
  }
  
  /**
   * Load groups domains
   *
   * @return CGroupDomain[]
   */
  function loadRefsGroupDomains() {
    if ($this->_ref_group_domains) {
      return $this->_ref_group_domains;
    }
    
    return $this->_ref_group_domains = $this->loadBackRefs("group_domains");
  }

  /**
   * Count objects
   *
   * @return int
   */
  function countObjects() {
    $idex      = new CIdSante400();
    $idex->tag = $this->tag;
    $this->_count_objects = $idex->countMatchingList();
    
    $where = array(
      "tag" => " = '$this->tag'"
    );    

    return $this->_detail_objects = $idex->countMultipleList($where, null, "object_class", null, array("object_class"), "tag");
  }
  
   /**
   * Merge an array of objects
    *
   * @param array $objects An array of CMbObject to merge
   * @param bool  $fast    Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   *
    * @return CMbObject
   */
  function merge($objects, $fast = false) {
    if (!$this->_force_merge) {
      return "CDomain-merge_impossible";
    }
    
    return parent::merge($objects, $fast);
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle ? $this->libelle : $this->tag;
  }

  /**
   * If domain is master
   *
   * @return bool
   */
  function isMaster() {
    foreach ($this->loadRefsGroupDomains() as $_group_domain) {
      if ($_group_domain->isMasterIPP()) {
        return $this->_is_master_ipp = true;
      }
      
      if ($_group_domain->isMasterNDA()) {
        return $this->_is_master_nda = true;
      }
    }

    return false;
  }

  /**
   * Get master domain tag
   *
   * @param string $domain_type Object class
   * @param string $group_id    Group
   *
   * @return CDomain
   */
  static function getMasterDomain($domain_type, $group_id = null) {
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    $group_domain = new CGroupDomain();
    $group_domain->object_class = $domain_type;
    $group_domain->group_id     = $group_id;
    $group_domain->master       = true;
    $group_domain->loadMatchingObject();
    
    $domain = new CDomain();
    $domain->load($group_domain->domain_id);
    
    return $domain;
  }
} 