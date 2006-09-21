<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage admin
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

global $permissionSystemeDown;
$sql = "SHOW TABLE STATUS LIKE 'perm_module'";
$permissionSystemeDown = !db_loadResult($sql);
 
if(!defined("PERM_DENY")) {
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
  
  // References
  var $_ref_db_user   = null;
  var $_ref_db_module = null;

  function CPermModule() {
    $this->CMbObject("perm_module", "perm_module_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["user_id"]     = "ref|notNull";
    $this->_props["mod_id"]      = "num|notNull";
    $this->_props["permission"]  = "num|notNull";
    $this->_props["view"]        = "num|notNull";
  }
  
  function loadRefDBModule() {
    $this->_ref_db_module = new CModule;
    $this->_ref_db_module->load($this->mod_id);
  }

  function loadRefDBUser() {
    $this->_ref_db_user = new CUser;
    $this->_ref_db_user->load($this->user_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefDBModule();
    $this->loadRefDBUser();
  }
  
  // Those functions are statics
  
  function getPermModule($mod_id, $permType) {
    return(CPermModule::getInfoModule("permission", $mod_id, $permType));
  }
  
  function getViewModule($mod_id, $permType) {
    return(CPermModule::getInfoModule("view", $mod_id, $permType));
  }
  
  function getInfoModule($field, $mod_id, $permType, $user_id = null) {
    global $AppUI, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    if(!$user_id) {
      $user_id = $AppUI->user_id;
    }
    $permModule = new CPermModule;
    $where = array();
    $where["user_id"] = "= '$user_id'";
    $where["mod_id"]  = "= '0'"; // Tous les modules
    $where["$field"]    = ">= '$permType'";
    if(count($permModule->loadList($where))) {
      $where["mod_id"] = "= '$mod_id'";
      $where["$field"]   = "< '$permType'";
      return !count($permModule->loadList($where));
    } else {
      $where["mod_id"] = "= '$mod_id'";
      $where["$field"]   = ">= '$permType'";
      return count($permModule->loadList($where));
    }
  }

  // Return the first visible module
  function getVisibleModule() {
    $listModules = CModule::getVisible();
    foreach($listModules as $module) {
      if(CPermModule::getViewModule($module->mod_id, PERM_READ)) {
        return $module->mod_name;
      }
    }
    return false;
  }
  
  // Return all the visible modules
  function getVisibleModules() {
    $listReadable = array();
    $listModules = CModule::getVisible();
    foreach($listModules as $module) {
      if(CPermModule::getViewModule($module->mod_id, PERM_READ)) {
        $listReadable[] = $module;
      }
    }
    return $listReadable;
  }
}

?>