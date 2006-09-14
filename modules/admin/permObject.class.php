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
 * The CPermObject class
 */
class CPermObject extends CMbObject {
  // DB Table key
  var $perm_object_id = null;
  
  // DB Fields
  var $user_id      = null;
  var $object_id    = null;
  var $object_class = null;
  var $permission   = null;
  
  function CPermObject() {
    $this->CMbObject("perm_object", "perm_object_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["user_id"]      = "ref|notNull";
    $this->_props["object_id"]    = "ref|notNull";
    $this->_props["object_class"] = "str|notNull";
    $this->_props["permission"]   = "num|notNull";
  }
  
  
  
  // Those functions are statics
  
  function getPermObject($user_id, $object, $permType) {
    $where = array();
    $where["user_id"]      = "= '$user_id'";
    $where["object_id"]    = "= '0'";
    $where["object_class"] = "= '".get_class($object)."'";
    $where["permission"]   = ">= '$permType'";
    if(count($this->loadList($where))) {
      $where["object_id"]  = "= '$object->_id'";
      $where["permission"] = "< '$permType'";
      if(count($this->loadList($where))) {
        return false;
      } else {
        return true;
      }
    } else {
      $where["object_id"]  = "= '$object->_id'";
      $where["permission"] = ">= '$permType'";
      if(count($this->loadList($where))) {
        return false;
      } else {
        return $object->_ref_module->getPerm($user_id, $permType);
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