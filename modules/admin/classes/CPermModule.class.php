<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage admin
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CPermModule class
 */
class CPermModule extends CMbObject {
  // Constants
  const DENY = 0;
  const READ = 1;
  const EDIT = 2;
  
  // Stored permissions
  static $users_perms = null;    // OLD query system
//  static $users_perms = array(); // NEW query system
  static $users_cache = array();

  static $pair_deny = array(
    "permission" => CPermModule::DENY, 
    "view"       => CPermModule::DENY,
  );
  
  // DB Table key
  public $perm_module_id;

  // DB Fields
  public $user_id;
  public $mod_id;
  public $permission;
  public $view;
  
  // Distant fields
  public $_owner;
  
  // References
  public $_ref_db_user;
  public $_ref_db_module;
  
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
    $specs["permission"]  = "enum list|0|1|2";
    $specs["view"]        = "enum list|0|1|2";

    $specs["_owner"]        = "enum list|user|template";
    return $specs;
  }

  /**
   * @return CModule
   */
  function loadRefDBModule() {
    return $this->_ref_db_module = $this->loadFwdRef("mod_id", true);
  }

  /**
   * @return CUser
   */
  function loadRefDBUser() {
    return $this->_ref_db_user = $this->loadFwdRef("user_id", true);
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
   *
   * @param int $user_id The concerned user, connected user if null
   *
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
    foreach ($perms as $_perms) {
      foreach ($_perms as $_perm) {
        self::$users_perms[$user->_id][$_perm->mod_id ? $_perm->mod_id : "all"] = array(
          "permission" => $_perm->permission,
          "view"       => $_perm->view,
        );
      }
    }
  }  
  
  static function loadUserPerms($user_id = null) {
    global $userPermsModules;
    
    // Déclaration du user
    $user = CUser::get($user_id);

    // Declaration des tableaux de droits
    $permsFinal = array();
    $tabModProfil = array();
    $tabModSelf = array();
    
    // Chargement des droits
    $permsProfil = CPermModule::loadExactPerms($user->profile_id);
    $permsSelf = CPermModule::loadExactPerms($user->user_id);
    
    // Creation du tableau de droit de permsSelf
    foreach ($permsSelf as $value) {
      $tabModSelf["mod_$value->mod_id"] = $value;
    }
    
    // Creation du tableau de droit de permsProfil
    foreach ($permsProfil as $value) {
      $tabModProfil["mod_$value->mod_id"] = $value;
    }
    
    // Fusion des deux tableaux de droits
    $tabModFinal = array_merge($tabModProfil, $tabModSelf);
    
    // Creation du tableau de fusion des droits
    foreach ($tabModFinal as $value) {
      $permsFinal[$value->perm_module_id] = $value;
    }

    // Tri du tableau de droit final en fonction des cle (perm_module_id)
    ksort($permsFinal);

    $listPermsModules = $permsFinal;
    if ($user_id !== null) {
      $currPermsModules = array();
      foreach ($listPermsModules as $perm_mod) {
        if (!$perm_mod->mod_id) {
          $currPermsModules[0] = $perm_mod;
        }
        else {
          $currPermsModules[$perm_mod->mod_id] = $perm_mod;
        }
      }
      return $currPermsModules;
    }
    else {
      $userPermsModules = array();
      foreach ($listPermsModules as $perm_mod) {
        if (!$perm_mod->mod_id) {
          $userPermsModules[0] = $perm_mod;
        }
        else {
          $userPermsModules[$perm_mod->mod_id] = $perm_mod;
        }
      }
      
      return $userPermsModules;
    }
  }
  
  static function getPermModule($mod_id, $permType = null, $user_id = null) {
    return CPermModule::getInfoModule("permission", $mod_id, $permType, $user_id);
  }
  
  static function getViewModule($mod_id, $permType = null, $user_id = null) {
    return CPermModule::getInfoModule("view", $mod_id, $permType, $user_id);
  }
  
  static function getInfoModule($field, $mod_id, $permType = null, $user_id = null) {
    $user = CUser::get($user_id);
  
    // Use permission query cache when available
    if (isset(self::$users_cache[$user->_id][$mod_id])) {
      return self::$users_cache[$user->_id][$mod_id][$field] >= $permType;
    }
    
    // New cached permissions system : DO NOT REMOVE
    if (is_array(self::$users_perms)) {
      self::buildUser($user->_id);
      $perms = self::$users_perms[$user->_id];
      
      // Module specific, or All modules, or DENY
      $perm =  
        (isset($perms[$mod_id]) ? $perms[$mod_id] :
        (isset($perms["all"  ]) ? $perms["all"  ] : self::$pair_deny));

      // Register cache
      self::$users_cache[$user->_id][$mod_id] = $perm;
      return $permType === null ? $perm[$field] : $perm[$field] >= $permType;
    }

    // Old permission system    
    global $userPermsModules;
    
    $result = PERM_DENY;
    if ($user_id !== null) {
      $perms = CPermModule::loadUserPerms($user_id);
    }
    else {
      $perms =& $userPermsModules;
    }
    
    if (isset($perms[0])) {
      $result = $perms[0]->$field;
    }
    
    if (isset($perms[$mod_id])) {
      if (!$mod_id) {
        $result = $perms[0]->$field;
      }
      else {
        $result = $perms[$mod_id]->$field;
      }
    }

    return $result >= $permType;
  }

  /**
   *  Return the first visible module
   *
   * @return bool|string The module name or false
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
   *
   * @return CModule[]
   */
  static function getVisibleModules() {
    $listReadable = array();
    $listModules = CModule::getVisible();
    foreach ($listModules as $module) {
      if (CPermModule::getViewModule($module->mod_id, PERM_READ)) {
        $listReadable[$module->mod_name] = $module;
      }
    }
    return $listReadable;
  }
}

if (is_null(CPermModule::$users_perms)) {
  CPermModule::loadUserPerms();
}
