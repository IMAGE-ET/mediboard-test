<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbobject");

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
class CModule extends CMbObject {
  // Static Collections
  static $installed = array();
	static $active    = array();
	static $visible   = array();
 
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
  var $_latest     = null;
  var $_upgradable = null;
  var $_configable = null;
  
  // Other fields
  var $_dsns = null;
  
  // Other collections
  var $_tabs      = null;  // List of tabs with permission
  var $_can       = null;  // Rights

  function CModule() {
    parent::__construct();
   
    // Hack to simulate the activeness of the class which has no real module 
    $this->_ref_module = $this;
  }
  
	/**
	 * Get all classes for a given module
	 * @param $module string Module name
	 * @return array[string] Class names
	 **/
	function getClassesFor($module) {
		// Liste des Class
		$listClass = getInstalledClasses();
		
		$tabClass = array();
		foreach ($listClass as $class) {
  		$object = new $class;
  		if (!$object->_ref_module) {
  			continue;
  		}
  		if ($object->_ref_module->mod_name == $module) {
  			$tabClass[] = $object->_class_name;
  		}
  	}
  	return $tabClass;
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modules';
    $spec->key   = 'mod_id';
    return $spec;
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["messages"]            = "CMessage module_id";
	  $backProps["permissions_modules"] = "CPermModule mod_id";
	  return $backProps;
	}
  
  function getProps() {
  	$props = parent::getProps();
    $props["mod_name"]      = "str notNull maxLength|20";
    $props["mod_type"]      = "enum notNull list|core|user";
    $props["mod_version"]   = "str notNull maxLength|6";
    $props["mod_active"]    = "bool";
    $props["mod_ui_active"] = "bool";
    $props["mod_ui_order"]  = "num";

    $props["_latest"]       = "str notNull maxLength|6";
    $props["_upgradable"]   = "bool";
    $props["_configable"]   = "bool";
    
    $props["_dsns"]   = "";
    
    return $props;
  }
  
  /**
   * Load and compare a module to a given setup
   * @param $setup CSetup
   */
  function compareToSetup(CSetup $setup) {
    $this->mod_name = $setup->mod_name;
    $this->loadMatchingObject();
    $this->mod_type = $setup->mod_type;
    $this->_latest  = $setup->mod_version;
    $this->_upgradable = $this->mod_version != $this->_latest;
    $this->_configable = is_file("modules/$this->mod_name/configure.php");
    $this->_dsns = $setup->getDatasources();
    
    if (!$this->_id) {
      $this->mod_ui_order = 100;
    }
  }
  
  function updateFormFields() {
    $this->_view = CAppUI::tr("module-$this->mod_name-court");
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
    $this->_canEdit = $this->getView(PERM_EDIT);
    return $this->_canEdit;
  }
  
  function canRead() {
    $this->_canRead = $this->getPerm(PERM_READ);
    return $this->_canRead;
  }
  
  function canEdit() {
    $this->_canEdit = $this->getPerm(PERM_EDIT);
    return $this->_canEdit;
  }
  
  function canDo(){
  	if(!$this->_can) {
  	  $canDo = new CCanDo;
  	  $canDo->read  = $this->canRead();
      $canDo->edit  = $this->canEdit();
      $canDo->view  = $this->canView();
      $canDo->admin = $this->canAdmin();
      $this->_can =  $canDo;
      return $canDo;
  	}
  	return $this->_can;
  }

  static function loadModules() {
    $modules = new CModule;
    $order = "mod_ui_order";
    $modules = $modules->loadList(null, $order);    
    foreach ($modules as &$module) {
      self::$installed[$module->mod_name] =& $module;
      if($module->mod_active == 1) {
        self::$active[$module->mod_name] =& $module;  
      } 
      if($module->mod_ui_active == 1) {
        self::$visible[$module->mod_name] =& $module;
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
    if (!$this->checkActive()) {
      return;
    }
    
    global $uistyle, $AppUI, $tab, $a, $action, $actionType;

    // Add configure tab if exist
    $configPath = "./modules/$this->mod_name/configure.php";
    if (is_file($configPath) && ($AppUI->user_type == 1)){
      $this->registerTab("configure", "Configure", TAB_ADMIN);
    }

    // Try to access wanted tab
    $tabPath = "./modules/$this->mod_name/$tab.php";
    if (!is_file($tabPath)) {
      $tab = $this->_tabs[0][0];
      $tabPath = "./modules/$this->mod_name/$tab.php";
      if (!is_file($tabPath)) {
        $AppUI->redirect("m=system&a=access_denied");
      }
    }
    
    $AppUI->savePlace();
    
    // Tab becomes an action if unique
    if (count($this->_tabs) == 1) {
      $a = $tab;
      $this->showAction();
      return;
    }

    $action     = $tab;
    $actionType = "tab";
    
    // Show template
    $smartyStyle = new CSmartyDP("style/$uistyle");
    $smartyStyle->assign("tabs"   , $this->_tabs);
    $smartyStyle->assign("tab"    , $tab);

    $smartyStyle->assign("fintab" , false);
    $smartyStyle->display("tabbox.tpl");

    require_once $tabPath;
  
    $smartyStyle->assign("fintab", true);
    $smartyStyle->display("tabbox.tpl");
    
  }
  
  function showAction() {
    if (!$this->checkActive()) {
      return;
    }
    
    global $u, $a, $action, $actionType;
    $action     = $a;
    $actionType = "a";
    $actionPath = "./modules/$this->mod_name/";
    $actionPath .= $u ? "$u/" : "";
    $actionPath .= "$a.php";
    require_once $actionPath;
  }
  
  function checkActive() {
    if (!$this->mod_active) {
      $smarty = new CSmartyDP("modules/system");
      $smarty->display("module_inactive.tpl");
      return false;
    }
    
    return true;
  }
  
  /**
   * Check if a module exist
   * @param string $moduleName
   * @return bool
   */
  static function exists($moduleName) {
    return is_dir("./modules/$moduleName"); 
  }
  
  /**
   * Returns all or a named installed module
   */
  static function getInstalled($moduleName = null) {
    if ($moduleName) {
      return isset(self::$installed[$moduleName]) ? self::$installed[$moduleName] : null;
    }

    return self::$installed;
  }

  /**
   * Returns all or a named active module
   */
  static function getActive($moduleName = null) {
    if ($moduleName) {
      return isset(self::$active[$moduleName]) ? self::$active[$moduleName] : null;
    }

    return self::$active;
  }
   
  /**
   * Returns all or a named visible module
   */
  static function getVisible($moduleName = null) {
    if ($moduleName) {
      return isset(self::$visible[$moduleName]) ? self::$visible[$moduleName] : null;
    }

    return self::$visible;
  }
  
  /**
   * get CanDo object for given installed module, 
   * @return CanDo with no permission if module not installed 
   */
  function getCanDo($moduleName) {
    $module = CModule::getInstalled($moduleName);
    return $module ? $module->canDo() : new CCanDo;
  }
  
  function reorder() {
    $sql = "SELECT * FROM modules ORDER BY mod_ui_order";
    $result = $this->_spec->ds->exec($sql);
    $i = 1;
    while($row = $this->_spec->ds->fetchArray($result)) {
      $sql = "UPDATE modules SET mod_ui_order = '$i' WHERE mod_id = '".$row["mod_id"]."'";
      $this->_spec->ds->exec($sql);
      $i++;
    }
  }

  function install() {
    $sql = "SELECT mod_name FROM modules WHERE mod_name = '$this->mod_name'";
    if ($this->_spec->ds->loadHash($sql)) {
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
    if (!$this->_spec->ds->exec( $sql )) {
      return $this->_spec->ds->error();
    } else {
      $this->reorder();
      $sql = "DELETE FROM perm_module WHERE mod_id = $this->mod_id";
      $this->_spec->ds->exec( $sql );
      return null;
    }
  }

  function move($dirn) {
    $temp = $this->mod_ui_order;
    if($dirn == "moveup") {
      $temp--;
      $sql = "UPDATE modules SET mod_ui_order = (mod_ui_order+1) WHERE mod_ui_order = $temp";
      $this->_spec->ds->exec($sql);
    } else if($dirn == "movedn") {
      $temp++;
      $sql = "UPDATE modules SET mod_ui_order = (mod_ui_order-1) WHERE mod_ui_order = $temp";
      $this->_spec->ds->exec($sql);
    }
    $sql = "UPDATE modules SET mod_ui_order = $temp WHERE mod_id = $this->mod_id";
    $this->_spec->ds->exec($sql);

    $this->mod_id = $temp;
    
    $this->reorder();
  }
}

CModule::loadModules();

?>