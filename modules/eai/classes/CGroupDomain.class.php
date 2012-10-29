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
  var $group_domain_id = null;
  
  // DB fields
  var $group_id        = null;
  var $domain_id       = null;
  var $object_class    = null;
  var $master          = null;
  
  /**
   * @var CGroup
   */
  var $_ref_group      = null; 
  
  /**
   * @var CDomain
   */
  var $_ref_domain     = null;     
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'group_domain';
    $spec->key   = 'group_domain_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["group_id"]     = "ref notNull class|CGroups autocomplete|text";
    $props["domain_id"]    = "ref notNull class|CDomain";
    $props["object_class"] = "enum notNull list|CPatient|CSejour";
    $props["master"]       = "bool notNull";

    return $props;
  } 
  
  /**
   * @return CGroup
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  /**
   * @return CDomain
   */
  function loadRefDomain() {
    return $this->_ref_domain = $this->loadFwdRef("domain_id", 1);
  }
  
  
} 