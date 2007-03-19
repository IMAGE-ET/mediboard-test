<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author Romain Ollivier
*/
 
global $AppUI, $m;

require_once($AppUI->getSystemClass("mbobject"));

if(!defined("TAB_READ")) {
  define("TAB_READ"  , "0");
  define("TAB_EDIT"  , "1");
  define("TAB_ADMIN" , "2");
}

if(!defined("PERM_DENY")) {
  define("PERM_DENY" , "0");
  define("PERM_READ" , "1");
  define("PERM_EDIT" , "2");
}

/**
* Module class
*/

global $module_installed;
global $module_active;
global $module_visible;

class CModule extends CMbObject {
  
  // primary key
  var $mod_id;
  
  // DB Fields
  var $mod_name      = null;
  var $mod_type      = null; // Core or User
  var $mod_version   = null; // Current Installed version MM.mmm
  var $mod_active    = null; // active module
  var $mod_ui_active = null; // visible module
  var $mod_ui_order  = null; // UI Position

  // Form Fields
  var $_tabs      = null;  // List of tabs with permission
  var $_upgradable = null; // Check if upgradable
  
  // Static Collections
  var $_registered = array();

  function CModule() {
    $this->CMbObject("modules", "mod_id");
  }

  function getSpecs() {
    return array (
      "mod_name"      => "notNull str maxLength|20",
      "mod_type"      => "notNull enum list|core|user",
      "mod_version"   => "notNull str maxLength|6",
      "mod_ui_active" => "notNull num length|1", // should be "bool"
      "mod_ui_order"  => "notNull num"
    );
  }
  
  function updateFormFields() {
    $this->_view = $this->mod_name;
  }
  
  function loadByName($name) {
    $where = array();
    $where["mod_name"] = "= '$name'";
    return $this->loadObject($where);
  }
  
  function getPerm($permType) {
    return CPermModule::getPermModule($this->mod_id, $permType);
  }
  
  function getView($permType) {
    return CPermModule::getViewModule($this->mod_id, $permType);
  }
  
  function canView() {
    $this->_canView = $this->getView(PERM_READ);
    return $this->_canView;
  }
  
  function canAdmin() {
    $this->_canView = $this->getView(PERM_EDIT);
    return $this->_canView;
  }
  
  function canRead() {
    $this->_canEdit = $this->getPerm(PERM_READ);
    return $this->_canEdit;
  }
  
  function canEdit() {
    $this->_canEdit = $this->getPerm(PERM_EDIT);
    return $this->_canEdit;
  }
  
  function canDo(){
    $canDo = new CCanDo;
    $canDo->read  = $this->canRead();
    $canDo->edit  = $this->canEdit();
    $canDo->view  = $this->canView();
    $canDo->admin = $this->canAdmin();
    
    return $canDo;
  }
  
  function registerSetup() {
    global $registeredModules;

    if (array_key_exists($this->name, $registeredModules)) {
      trigger_error("Module '$this->name' already registered", E_USER_ERROR);
    }
    
    $registeredModules[$this->name] = $this;
  }

  function loadModules() {
    $modules = new CModule;
    $order = "mod_ui_order";
    $modules = $modules->loadList(null, $order);
    
    global $module_installed;
    global $module_active;
    global $module_visible;

    $module_installed = array();
    $module_active = array();
    $module_visible = array();
    
    foreach($modules as $keyModule => $valModule) {
      $module =& $modules[$keyModule];
        
      $module_installed[$module->mod_name] = $module;
      
      if($module->mod_active == 1) {
        $module_active[$module->mod_name] =& $module;
      }
    
      if($module->mod_ui_active == 1) {
        $module_visible[$module->mod_name] =& $module;
      }
    }
  }
  
  function registerTab($file, $name, $permType) {
    switch($permType) {
      case TAB_READ:
        if($this->canRead()) {
          $this->_tabs[] = array($file, $name);
        }
        break;
      case TAB_EDIT:
        if($this->canEdit()) {
          $this->_tabs[] = array($file, $name);
        }
        break;
      case TAB_ADMIN:
        if($this->canAdmin()) {
          $this->_tabs[] = array($file, $name);
        }
        break;
    }
  }
  
  function showTabs() {
    global $uistyle, $AppUI, $tab, $a, $action, $actionType;

    if(!is_file("./modules/".$this->mod_name."/".$tab.".php")) {
      $tab = $this->_tabs[0][0];
    }
    
    $AppUI->savePlace();
    
    $moduleAdmin = CModule::getInstalled("system");
    
    if($moduleAdmin->canAdmin() && is_file("./modules/".$this->mod_name."/configure.php")){
      $this->registerTab("configure", "Configurer", TAB_READ);
    }
    
    if(count($this->_tabs) == 1) {
      $a = $tab;
      $this->showAction();
      return;
    }

    $action     = $tab;
    $actionType = "tab";

    require_once($AppUI->getSystemClass("smartydp"));
    $smartyStyle = new CSmartyDP("style/$uistyle");
    
    $smartyStyle->assign("tabs"   , $this->_tabs);
    $smartyStyle->assign("tab"    , $tab);
    $smartyStyle->assign("fintab" , false);
      
    $smartyStyle->display("tabbox.tpl");
    require_once "./modules/".$this->mod_name."/".$tab.".php";
  
    $smartyStyle->assign("fintab", true);
    $smartyStyle->display("tabbox.tpl");
  }
  
  function showAction() {
    global $AppUI, $u, $a, $action, $actionType;
    $action     = $a;
    $actionType = "a";
    require_once "./modules/".$this->mod_name."/".($u ? "$u/" : "")."$a.php";
  }
  
  /**
   * Returns all or a named installed module
   */
  function getInstalled($moduleName = null) {
    global $module_installed;

    if ($moduleName) {
      return @$module_installed[$moduleName];
    }

    return $module_installed;
  }

  /**
   * Returns all or a named active module
   */
  function getActive($moduleName = null) {
    global $module_active;

    if ($moduleName) {
      return @$module_active[$moduleName];
    }

    return $module_active;
  }
   
  /**
   * Returns all or a named visible module
   */
  function getVisible($moduleName = null) {
    global $module_visible;

    if ($moduleName) {
      return @$module_visible[$moduleName];
    }

    return $module_visible;
  }
  
  function reorder() {
    $sql = "SELECT * FROM modules ORDER BY mod_ui_order";
    $result = db_exec($sql);
    $i = 1;
    while($row = db_fetch_array($result)) {
      $sql = "UPDATE modules SET mod_ui_order = '$i' WHERE mod_id = '".$row["mod_id"]."'";
      db_exec($sql);
      $i++;
    }
  }

  function install() {
    $sql = "SELECT mod_name FROM modules WHERE mod_name = '$this->mod_name'";
    $temp = null;
    if (db_loadHash($sql, $temp)) {
      // the module is already installed
      // TODO: check for older version - upgrade
      return false;
    }
    $this->store();
    $this->reorder();
    return true;
  }

  function remove() {
    $sql = "DELETE FROM modules WHERE mod_id = $this->mod_id";
    if (!db_exec( $sql )) {
      return db_error();
    } else {
      $this->reorder();
      $sql = "DELETE FROM perm_module WHERE mod_id = $this->mod_id";
      db_exec( $sql );
      return null;
    }
  }

  function move($dirn) {
    $temp = $this->mod_ui_order;
    if($dirn == "moveup") {
      $temp--;
      $sql = "UPDATE modules SET mod_ui_order = (mod_ui_order+1) WHERE mod_ui_order = $temp";
      db_exec($sql);
    } else if($dirn == "movedn") {
      $temp++;
      $sql = "UPDATE modules SET mod_ui_order = (mod_ui_order-1) WHERE mod_ui_order = $temp";
      db_exec($sql);
    }
    $sql = "UPDATE modules SET mod_ui_order = $temp WHERE mod_id = $this->mod_id";
    db_exec($sql);

    $this->mod_id = $temp;
    
    $this->reorder();
  }
// overridable functions
  function moduleInstall() {
    return null;
  }
  function moduleRemove() {
    return null;
  }
  function moduleUpgrade() {
    return null;
  }
}

CModule::loadModules();

?>