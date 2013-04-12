<?php

/**
 * Identification group domain
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CGroupDomain
 * Identification group domain
 */

class CGroupDomain extends CMbObject {
  // DB Table key
  public $group_domain_id;
  
  // DB fields
  public $object_class;
  public $group_id;
  public $domain_id;
  public $master;
  
  /**
   * @var CGroup
   */
  public $_ref_group; 
  
  /**
   * @var CDomain
   */
  public $_ref_domain;     
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'group_domain';
    $spec->key   = 'group_domain_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["object_class"] = "enum notNull list|CPatient|CSejour";
    $props["group_id"]     = "ref notNull class|CGroups autocomplete|text";
    $props["domain_id"]    = "ref notNull class|CDomain";
    $props["master"]       = "bool notNull";

    return $props;
  } 
  
  /**
   * @return CGroup
   */
  function loadRefGroup() {
    if ($this->_ref_group) {
      return;
    }
    
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  /**
   * @return CDomain
   */
  function loadRefDomain() {
    if ($this->_ref_domain) {
      return;
    }
    
    return $this->_ref_domain = $this->loadFwdRef("domain_id", 1);
  }
  
  function loadView() {
    parent::loadView();
  }
  
  function check() {
    parent::check();
    
    $this->completeField("domain_id", "object_class", "group_id");
    
    // Recherche si on a un établissement du domaine déjà en master avec le même type d'objet et le même établissement
    if ($this->master) {
      $group_domain               = new CGroupDomain();
      $group_domain->object_class = $this->object_class;
      $group_domain->group_id     = $this->group_id;
      $group_domain->master       = 1;
  
      if ($group_domain->loadMatchingObject()) {
        return "CGroupDomain-master_already_exist";
      }
    }
    
    // Recherche si on a pas déjà un établissement du domaine pour un type d'objet différent
    $ljoin = array(
      "domain" => "domain.domain_id = group_domain.domain_id"
    );
    $where = array(
      "domain.domain_id" => " = '$this->domain_id'",
      "incrementer_id"   => "IS NOT NULL",
      "object_class"     => " != '$this->object_class'"
    );
    $group_domain = new CGroupDomain();
    if ($group_domain->countList($where, null, $ljoin) > 0) {
      return "CGroupDomain-object_class_already_exist";
    }
  }

  function merge() {
    return "CGroupDomain-merge_impossible";
  }
  
  function isMasterIPP() {
    return $this->master && ($this->object_class == "CPatient");
  }
  
  function isMasterNDA() {
    return $this->master && ($this->object_class == "CSejour");
  }
} 