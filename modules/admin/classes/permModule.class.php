<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CPermModule class
 */
class CPermModule extends CMbObject {
  static $system_down = false;

  // Constants
  const DENY = 0;
  const READ = 1;
  const EDIT = 2;
  
  // Permissions for connected user
  static $users_perms = null;
  static $users_queries = null;
	
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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perm_module';
    $spec->key   = 'perm_module_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["user_id"]     = "ref notNull class|CUser cascade";
    $specs["mod_id"]      = "ref class|CModule";
    $specs["permission"]  = "numchar notNull maxLength|1";
    $specs["view"]        = "numchar notNull maxLength|1";
    return $specs;
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
  static function loadExactPerms($user_id = null){
    $perm = new CPermModule;
    $where = array(
      "user_id" => "= '$user_id'"
    );
    return $perm->loadList($where);
  }
	
  /**
   * Build the class object permission tree for given user
   * Cache the result as static member
   * @param user_id ref|CUser The concerned user, connected user if null
   * @return void
   */
  static function buildUser($user_id = null) {
    $user = CUser::get($user_id);

    // Never reload permissions for a given user
    if (isset(self::$users_perms[$user->_id])) {
      return;
    }
    
    $perm = new CPermModule;

    // Profile specific permissions
    $perms["prof"] = array();
    if ($user->profile_id) {
      $perm->user_id = $user->profile_id;
      $perms["prof"] = $perm->loadMatchingList();
    }

    // User specific permissions
    $perm->user_id = $user->_id;
    $perms["user"] = $perm->loadMatchingList();
    
    // Build final tree
    foreach($perms as $owner => $_perms) {
      foreach($_perms as $_perm) {
        self::$users_perms[$user->_id][$_perm->mod_id ? $_perm->mod_id : "all"] = array(
				  "permission" => $_perm->permission,
					"view"       => $_perm->view,
				);
      }
    }
    
    self::$users_queries = array();
    mbTrace(self::$users_perms);
  }	
  
  static function loadUserPerms($user_id = null) {
    if (CPermModule::$system_down) {
      return true;
    }
    
    global $AppUI, $userPermsModules;
    
    // Déclaration du user
    $user = new CUser();
    if($user_id !== null){
      $user->load($user_id);
    } else {
      $user->load($AppUI->user_id);
    }

    //Declaration des tableaux de droits 
    $permsProfil = array();
    $permsSelf = array();
    $permsFinal = array();
    
    // Declaration des tableaux de droits
    $tabModProfil = array();
    $tabModSelf = array();
    $tabModFinal = array();
    
    //Chargement des droits
    $permsProfil = CPermModule::loadExactPerms($user->profile_id);
    $permsSelf = CPermModule::loadExactPerms($user->user_id);
    
    // Creation du tableau de droit de permsSelf
    foreach($permsSelf as $key => $value){
      $tabModSelf["mod_$value->mod_id"] = $value;
    }
    
    // Creation du tableau de droit de permsProfil
    foreach($permsProfil as $key => $value){
      $tabModProfil["mod_$value->mod_id"] = $value;
    }
    
    // Fusion des deux tableaux de droits
    $tabModFinal = array_merge($tabModProfil, $tabModSelf);
    
    // Creation du tableau de fusion des droits
    foreach($tabModFinal as $mod => $value){
      $permsFinal[$value->perm_module_id] = $value;
    }

    // Tri du tableau de droit final en fonction des cle (perm_module_id)
    ksort($permsFinal);

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
    } 
		
		else {
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
  
  static function getPermModule($mod_id, $permType, $user_id = null) {
    return CPermModule::getInfoModule("permission", $mod_id, $permType, $user_id);
  }
  
  static function getViewModule($mod_id, $permType, $user_id = null) {
    return CPermModule::getInfoModule("view", $mod_id, $permType, $user_id);
  }
  
  static function getInfoModule($field, $mod_id, $permType, $user_id = null) {
    if (CPermModule::$system_down) {
      return true;
    }
    
    global $userPermsModules;
		
    $result = PERM_DENY;
    if($user_id !== null) {
      $perms = CPermModule::loadUserPerms($user_id);
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
  static function getFirstVisibleModule() {
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
  static function getVisibleModules() {
    $listReadable = array();
    $listModules = CModule::getVisible();
    foreach($listModules as $module) {
      if(CPermModule::getViewModule($module->mod_id, PERM_READ)) {
        $listReadable[$module->mod_name] = $module;
      }
    }
    return $listReadable;
  }
}


// Check if permission system is down
CPermModule::$system_down = !CSQLDataSource::get("std")->loadTable("perm_module");
CPermModule::loadUserPerms();
//CPermModule::buildUser();
//CPermModule::buildUser(10);

?>