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
  
  function getPerm($permType) {
    return $this->getPermModule($this->mod_id, $permType);
  }
  
  function getView($permType) {
    return $this->getViewModule($this->mod_id, $permType);
  }
  
  function canView() {
    $this->_canView = $this->getView(PERM_READ);
    return $this->_canView;
  }
  
  function canEdit() {
    $this->_canEdit = $this->getView(PERM_EDIT);
    return $this->_canEdit;
  }
  
  // Those functions are statics
  
  function getPermModule($mod_id, $permType) {
    return($this->getInfoModule("permission", $mod_id, $permType));
  }
  
  function getViewModule($mod_id, $permType) {
    return($this->getInfoModule("view", $mod_id, $permType));
  }
  
  function getInfoModule($field, $mod_id, $permType) {
    global $AppUI;
    $user_id = $AppUI->user_id;
    $where = array();
    $where["user_id"] = "= '$user_id'";
    $where["mod_id"]  = "= '0'"; // Tous les modules
    $where["$field"]    = ">= '$permType'";
    if(count($this->loadList($where))) {
      $where["mod_id"] = "= '$mod_id'";
      $where["$field"]   = "< '$permType'";
      return !count($this->loadList($where));
    } else {
      $where["mod_id"] = "= '$mod_id'";
      $where["$field"]   = ">= '$permType'";
      return !count($this->loadList($where));
    }
  }

  // Return the first readable module
  function getReadableModule() {
    $listModules = CModule::getVisible();
    foreach($listModules as $module) {
      if($this->getViewModule($module->mod_id, PERM_READ)) {
        return $module->mod_name;
      }
    }
    return false;
  }
  
  // Return all the readable modules
  function getReadableModules() {
    $listReadable = array();
    $listModules = CModule::getVisible();
    foreach($listModules as $module) {
      if($this->getViewModule($module->mod_id, PERM_READ)) {
        $listReadable[] = $module;
      }
    }
    return $listReadable;
  }
}

?>