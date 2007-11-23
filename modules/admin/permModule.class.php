<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage admin
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

global $permissionSystemeDown;
$sql = "SHOW TABLE STATUS LIKE 'perm_module'";

global $dPconfig;
$permissionSystemeDown = !CSQLDataSource::get("std")->loadResult($sql);

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
      "user_id"     => "notNull ref class|CUser",
      "mod_id"      => "ref class|CModule",
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
  
  // Chargement des droits du user
  function loadExactPerms($user_id = null){
    global $AppUI, $userPermsModules, $permissionSystemeDown;
    
    $perm = new CPermModule;
    $listPermsModules = array();
    $where = array();
    $where["user_id"] = "= '$user_id'";
    return $listPermsModules = $perm->loadList($where);
  }
  
  
  // Those functions are statics
  function loadUserPerms($user_id = null) {
    
    global $AppUI, $userPermsModules, $permissionSystemeDown;
    
    if($permissionSystemeDown) {
      return true;
    }
    
    
    // Déclaration du user
    $user = new CUser();
    if($user_id !== null){
      $user->load($user_id);
    } else {
      $user->load($AppUI->user_id);
    }
    
    $perm = new CPermModule;
    
    //Declaration des tableaux de droits 
    $permsProfil = array();
    $permsSelf = array();
    $permsFinal = array();
    
    // Declaration des tableaux de droits
    $tabModProfil = array();
    $tabModSelf = array();
    $tabModFinal = array();
    
    //Chargement des droits
    $permsProfil = $perm->loadExactPerms($user->profile_id);
    $permsSelf = $perm->loadExactPerms($user->user_id);
    
    // Creation du tableau de droit de permsSelf
    foreach($permsSelf as $key => $value){
      $tabModSelf["mod_".$value->mod_id] = $value;
    }
    
    // Creation du tableau de droit de permsProfil
    foreach($permsProfil as $key => $value){
      $tabModProfil["mod_".$value->mod_id] = $value;
    }
    
    // Fusion des deux tableaux de droits
    $tabModFinal = array_merge($tabModProfil, $tabModSelf);
    
    
    // Creation du tableau de fusion des droits
    foreach($tabModFinal as $mod => $value){
      $permsFinal[$value->perm_module_id] = $value;
    }

    // Tri du tableau de droit final en fonction des cle (perm_module_id)
    ksort($permsFinal);
    
    
    $listPermsModules = array();
    
    $listPermsModules = $permsFinal;
    if($user_id !== null) {
      $currPermsModules = array();
      foreach($listPermsModules as $perm_mod) {
        if(!$perm_mod->mod_id){
          $currPermsModules[0] = $perm_mod;
        }else{
          $currPermsModules[$perm_mod->mod_id] = $perm_mod;
        }
      }
      return $currPermsModules;
    } else {
      $userPermsModules = array();
      foreach($listPermsModules as $perm_mod) {
        if(!$perm_mod->mod_id){
          $userPermsModules[0] = $perm_mod;
        }else{
          $userPermsModules[$perm_mod->mod_id] = $perm_mod;
        }
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
      if(!$mod_id){
        $result = $perms[0]->$field;
      }else{
        $result = $perms[$mod_id]->$field;
      }
    }
    return $result >= $permType;
  }

  /**
   *  Return the first visible module
   */
  function getFirstVisibleModule() {
    $listModules = CModule::getVisible();
    foreach ($listModules as $module) {
      if (CPermModule::getViewModule($module->mod_id, PERM_READ)) {
        return $module->mod_name;
      }
    }
    return false;
  }
  
  /**
   *  Return all the visible modules
   */
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