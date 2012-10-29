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
  var $incrementer_id = null;
  var $actor_id       = null;
  var $actor_class    = null;
  var $tag            = null;
  
  /**
   * @var CInteropActor
   */
  var $_ref_actor       = null; 
  /**
   * @var CIncrementer
   */
  var $_ref_incrementer = null;     
  
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'domain';
    $spec->key   = 'domain_id';
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["incrementer_id"] = "ref notNull class|CIncrementer";
    $props["actor_id"]       = "ref class|CInteropActor meta|actor_class";
    $props["actor_class"]    = "str notNull maxLength|80";
    $props["tag"]            = "str notNull";

    return $props;
  } 
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["group_domains"] = "CGroupDomain domain_id";
    $backProps["incrementers"]  = "CIncrementer domain_id";
    
    return $backProps;
  }
  
  /**
   * @return CInteropActor
   */
  function loadRefActor(){
    return $this->_ref_actor = $this->loadFwdRef("actor_id");
  }
  
  /**
   * @return CIncrementer
   */
  function loadRefIncrementer(){
    return $this->_ref_incrementer = $this->loadFwdRef("incrementer_id");
  }
} 