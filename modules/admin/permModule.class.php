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
  }
  
  function getSpecs() {
    return array (
      "user_id"     => "notNull refMandatory",
      "mod_id"      => "ref",
      "permission"  => "notNull numchar maxLength|1",
      "view"        => "notNull numchar maxLength|1",
    );
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
  
  function loadUserPerms($user_id = null) {
    global $AppUI, $userPermsModules, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $perm = new CPermModule;
    $listPermsModules = array();
    $where = array();
    if($user_id !== null) {
      $where["user_id"] = "= '$user_id'";
    } else {
      $where["user_id"] = "= '$AppUI->user_id'";
    }
    $listPermsModules = $perm->loadList($where);
    if($user_id !== null) {
      $currPermsModules = array();
      foreach($listPermsModules as $perm_mod) {
        $currPermsModules[$perm_mod->mod_id] = $perm_mod;
      }
      return $currPermsModules;
    } else {
      $userPermsModules = array();
      foreach($listPermsModules as $perm_mod) {
        $userPermsModules[$perm_mod->mod_id] = $perm_mod;
      }
      return $userPermsModules;
    }
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
    if($user_id !== null) {
      $perms =& CPermModule::loadUserPerms($user_id);
    } else {
      $perms =& $userPermsModules;
    }
    if(isset($perms[0])) {
      $result = $perms[0]->$field;
    }
    if(isset($perms[$mod_id])) {
      $result = $perms[$mod_id]->$field;
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