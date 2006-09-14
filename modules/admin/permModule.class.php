<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage admin
 *  @version $Revision: $
 *  @author Romain Ollivier
 */
 
if(!defined("PERM_DENY") && 0) {
  define("PERM_DENY" , "0");
  define("PERM_READ" , "1");
  define("PERM_EDIT" , "2");
}

/**
 * The CPermModule class
 */
class CPermModule extends CMbObject {
  // DB Table key
  var $perm_module_id = null;

  // DB Fields
  var $user_id    = null;
  var $mod_id     = null;
  var $permission = null;
  var $view       = null;

  function CPermModule() {
    $this->CMbObject("perm_module", "perm_module_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["user_id"]     = "ref|notNull";
    $this->_props["mod_id"]      = "num|notNull";
    $this->_props["permission"]  = "num|notNull";
    $this->_props["view"]        = "num|notNull";
  }
  
  // Those functions are statics
  
  function getPermModule($user_id, $mod_id, $permType) {
    $where = array();
    $where["user_id"]    = "= '$user_id'";
    $where["mod_id"]     = "= '0'";
    $where["permission"] = ">= '$permType'";
    if(count($this->loadList($where))) {
      $where["mod_id"]     = "= '$mod_id'";
      $where["permission"] = "< '$permType'";
      if(count($this->loadList($where))) {
        return false;
      } else {
        return true;
      }
    } else {
      $where["mod_id"]     = "= '$mod_id'";
      $where["permission"] = ">= '$permType'";
      if(count($this->loadList($where))) {
        return false;
      } else {
        return true;
      }
    }
  }
  
  function getViewModule($user_id, $mod_id, $permType) {
    $where = array();
    $where["user_id"] = "= '$user_id'";
    $where["mod_id"]  = "= '0'";
    $where["view"]    = ">= '$permType'";
    if(count($this->loadList($where))) {
      $where["mod_id"] = "= '$mod_id'";
      $where["view"]   = "< '$permType'";
      if(count($this->loadList($where))) {
        return false;
      } else {
        return true;
      }
    } else {
      $where["mod_id"] = "= '$mod_id'";
      $where["view"]   = ">= '$permType'";
      if(count($this->loadList($where))) {
        return false;
      } else {
        return true;
      }
    }
  }

  // Return the first readable module
  function getReadableModule($user_id) {
    $listModules = CModule::getVisible();
    foreach($listModules as $module) {
      if($this->getViewModule($user_id, $module->mod_id, PERM_READ)) {
        return $module->mod_name;
      }
    }
    return false;
  }
}

?>