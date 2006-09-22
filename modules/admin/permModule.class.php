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
  
  function loadUserPerms() {
    global $AppUI, $userPermsModules, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $perm = new CPermModule;
    $listPermsModules = array();
    $where = array();
    $where["user_id"] = "= '$AppUI->user_id'";
    $listPermsModules = $perm->loadList($where);
    $userPermsModules = array();
    foreach($listPermsModules as $perm_mod) {
      $userPermsModules[$perm_mod->mod_id] = $perm_mod;
    }
    //mbTrace($userPermsModules);
  }
  
  function getPermModule($mod_id, $permType) {
    return(CPermModule::getInfoModule("permission", $mod_id, $permType));
  }
  
  function getViewModule($mod_id, $permType) {
    return(CPermModule::getInfoModule("view", $mod_id, $permType));
  }
  
  function getInfoModule($field, $mod_id, $permType, $user_id = null) {
    global $userPermsModules, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $result = PERM_DENY;
    if(isset($userPermsModules[0])) {
      $result = $userPermsModules[0]->$field;
    }
    if(isset($userPermsModules[$mod_id])) {
      $result = $userPermsModules[$mod_id]->$field;
    }
    return $result >= $permType;
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

CPermModule::loadUserPerms();

?>