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
  var $domain_id      = null;
  
  // DB fields
  var $incrementer_id    = null;
  var $actor_id          = null;
  var $actor_class       = null;
  var $tag               = null;
  var $libelle           = null;
  var $derived_from_idex = null;
  
  // Form fields 
  var $_is_master_ipp  = null;
  var $_is_master_nda  = null;
  var $_count_objects  = null;
  var $_detail_objects = array();
  var $_force_merge    = false;
  
  /**
   * @var CInteropActor
   */
  var $_ref_actor       = null; 
  /**
   * @var CIncrementer
   */
  var $_ref_incrementer   = null;
  var $_ref_group_domains = null; 
  
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'domain';
    $spec->key   = 'domain_id';
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["incrementer_id"]    = "ref class|CIncrementer nullify";
    $props["actor_id"]          = "ref class|CInteropActor meta|actor_class nullify";
    $props["actor_class"]       = "str maxLength|80";
    $props["tag"]               = "str notNull";
    $props["libelle"]           = "str";
    $props["derived_from_idex"] = "bool";
    
    $props["_is_master_ipp"] = "bool";
    $props["_is_master_nda"] = "bool";
    $props["_count_objects"] = "num";

    return $props;
  } 
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["group_domains"] = "CGroupDomain domain_id";
    
    return $backProps;
  }
  
  /**
   * @return CInteropActor
   */
  function loadRefActor() {
    if ($actor = $this->loadFwdRef("actor_id")) {
      return $this->_ref_actor = $actor;
    }
    
    return $this->_ref_actor = new CInteropActor();
  }
  
  /**
   * @return CIncrementer
   */
  function loadRefIncrementer() {
    if ($this->_ref_incrementer) {
      return $this->_ref_incrementer;
    }
    
    return $this->_ref_incrementer = $this->loadFwdRef("incrementer_id");
  }
  
  /**
   * @return array
   */
  function loadRefsGroupDomains() {
    if ($this->_ref_group_domains) {
      return $this->_ref_group_domains;
    }
    
    return $this->_ref_group_domains = $this->loadBackRefs("group_domains");
  }
  
  function countObjects() {
    $idex      = new CIdSante400();
    $idex->tag = $this->tag;
    $this->_count_objects = $idex->countMatchingList();
    
    $where = array(
      "tag" => " = '$this->tag'"
    );    

    $this->_detail_objects = $idex->countMultipleList($where, null, "object_class", null, array("object_class"), "tag");
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()){
      return $msg;
    }
  }
  
   /**
   * Merge an array of objects
   * @param array An array of CMbObject to merge
   * @param bool $fast Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   * @return CMbObject
   */
  function merge($objects, $fast = false) {
    if (!$this->_force_merge) {
      return "CDomain-merge_impossible";
    }
    
    parent::merge($objects, $fast);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle ? $this->libelle : $this->tag;
  }
  
  function isMaster() {
    foreach ($this->loadRefsGroupDomains() as $_group_domain) {
      if ($_group_domain->isMasterIPP()) {
        return $this->_is_master_ipp = true;
      }
      
      if ($_group_domain->isMasterNDA()) {
        return $this->_is_master_nda = true;
      }
    }
  }
    
  static function getTagMasterDomain($domain_type, $group_id = null) {
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
    
    return $domain->tag;
  }
} 