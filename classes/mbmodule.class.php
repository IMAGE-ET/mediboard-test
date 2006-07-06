<?php /* $Id: mbobject.class.php 31 2006-05-05 09:55:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */
 
global $AppUI;

require_once($AppUI->getSystemClass("mbobject"));

global $mbModule_installed;
global $mbModule_active;
global $mbModule_visible;

class CMbModule extends CMbObject {
  var $mod_id;
  
  // DB Fields
  var $mod_name = null;
  var $mod_type = null; // Core or User
  var $mod_version = null; // Current Installed version MM.mmm
  var $mod_active; // active module
  var $mod_ui_active = null; // visible module
  var $mod_ui_order = null; // UI Position
   
  // Deprecated DB Fields
  var $mod_directory; // Same as mod_name
  var $mod_setupclass; // Probably useless
  var $mod_ui_icon ; // worthless, use module name
  var $mod_description; // worthless, use localisation
    
  // Form Fields
  var $_upgradable = null; // Check if upgradable
  
  // Static Collections
  var $_registered = array();
  
  function CMbModule() {
    $this->CMbObject("modules", "mod_id");

    $this->_props["mod_name"] = "notNull|str|maxLength|20";
    $this->_props["mod_type"] = "notNull|enum|core|user";
    $this->_props["mod_version"] = "notNull|str|maxLength|6";
    $this->_props["mod_ui_active"] = "notNull|num|length|1"; // should be "bool"
    $this->_props["mod_ui_order"] = "notNull|nul|length|2";
  }
  
  function formFields() {
    $this->_view = $this->mod_name;
  }
    
  function registerSetup() {
    global $registeredModules;

    if (array_key_exists($this->name, $registeredModules)) {
      trigger_error("Module '$this->name' already registered", E_USER_ERROR);
    }
    
    $registeredModules[$this->name] = $this;
  }
  
  // -- Utility functions

  /**
   * @author MyttO
   *
   */
  function loadModules() {
    $modules = new CMbModule;
    $order = "mod_ui_order";
    $where = array();
    $modules = $modules->loadList($where, $order);
    
    global $mbModule_installed;
    global $mbModule_active;
    global $mbModule_visible;

    $mbModule_installed = array();
    $mbModule_active = array();
    $mbModule_visible = array();
    
    foreach ($modules as $keyModule => $valModule) {
      $module =& $modules[$keyModule];
        
      $mbModule_installed[$module->mod_name] = $module;
      
      if ($module->mod_active == 1) {
        $mbModule_active[$module->mod_name] =& $module;
      }
    
      if ($module->mod_ui_active == 1) {
        $mbModule_visible[$module->mod_name] =& $module;
      }
    }
    
  }
  /**
   * Returns all or a named installed module
   */
  function getInstalled($moduleName = null) {
    global $mbModule_installed;

    if ($moduleName) {
      return $mbModule_installed[$moduleName];
    }

    return $mbModule_installed;
  }

  /**
   * Returns all or a named active module
   */
  function getActive($moduleName = null) {
    global $mbModule_active;

    if ($moduleName) {
      return $mbModule_active[$moduleName];
    }

    return $mbModule_active;
  }
   
  /**
   * Returns all or a named visible module
   */
  function getVisible($moduleName = null) {
    global $mbModule_visible;

    if ($moduleName) {
      return $mbModule_visible[$moduleName];
    }

    return $mbModule_visible;
  }
   
  function getModule($moduleName) {
  }
}
 
// Static calls
CMbModule::loadModules();
?>