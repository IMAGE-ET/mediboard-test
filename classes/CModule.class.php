<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

if (!defined("TAB_READ")) {
  /**
   * Read permissions on the view
   */
  define("TAB_READ" , 0);

  /**
   * Edit permissions on the view
   */
  define("TAB_EDIT" , 1);

  /**
   * Admin permissions on the view
   */
  define("TAB_ADMIN", 2);
}

if (!defined("PERM_DENY")) {
  /**
   * No permission on the object
   */
  define("PERM_DENY", 0);

  /**
   * Read permission on the object
   */
  define("PERM_READ", 1);

  /**
   * Edit permission on the object
   */
  define("PERM_EDIT", 2);
}

/**
 * Module class
 */
class CModule extends CMbObject {
  // Static Collections
  static $installed = array();
  static $active    = array();
  static $visible   = array();
  static $absent    = array();

  // Primary key
  public $mod_id;
  
  // DB Fields
  public $mod_name;
  public $mod_type; // Core or User
  public $mod_version; // Current Installed version MM.mmm
  public $mod_active; // active module
  public $mod_ui_active; // visible module
  public $mod_ui_order; // UI Position

  // Form Fields
  public $_latest;
  public $_too_new;
  public $_upgradable;
  public $_configable;
  public $_files_missing;
  public $_dependencies;
  public $_dependencies_not_verified;
  public $_update_messages;
  
  // Other fields
  public $_dsns = array();
  
  // Other collections
  public $_tabs      = array(); // List of tabs with permission

  public $_default_tab;   // if pref used, direct go to this tab

  /**
   * @var bool
   * @deprecated
   */
  public $_canView;

  /**
   * constructor
   */
  function __construct() {
    parent::__construct();
   
    // Hack to simulate the activeness of the class which has no real module 
    $this->_ref_module = $this;
  }
  
  /**
   * Get all classes for a given module
   *
   * @param string $module Module name
   *
   * @return array[string] Class names
   **/
  static function getClassesFor($module) {
    // Liste des Class
    $listClass = CApp::getInstalledClasses();
    
    $tabClass = array();
    foreach ($listClass as $class) {
      $object = new $class;
      if (!is_object($object->_ref_module)) {
        continue;
      }

      if ($object->_ref_module->mod_name == $module) {
        $tabClass[] = $object->_class;
      }
    }
    return $tabClass;
  }

  /**
   * Specs
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modules';
    $spec->key   = 'mod_id';
    $spec->uniques["name"] = array("mod_name");
    return $spec;
  }

  /**
   * backprops
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages"]            = "CMessage module_id";
    $backProps["permissions_modules"] = "CPermModule mod_id";
    return $backProps;
  }

  /**
   * Class props
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["mod_name"]      = "str notNull maxLength|20";
    $props["mod_type"]      = "enum notNull list|core|user";
    $props["mod_version"]   = "str notNull maxLength|6";
    $props["mod_active"]    = "bool";
    $props["mod_ui_active"] = "bool";
    $props["mod_ui_order"]  = "num";

    $props["_latest"]       = "str notNull maxLength|6";
    $props["_too_new"]      = "bool";
    $props["_upgradable"]   = "bool";
    $props["_configable"]   = "bool";
    $props["_dependencies"] = "str";
    
    $props["_dsns"]   = "";
    
    return $props;
  }
  
  /**
   * Load and compare a module to a given setup
   *
   * @param CSetup $setup The CSetup object to compare to
   * 
   * @return void
   */
  function compareToSetup(CSetup $setup) {
    $this->mod_name = $setup->mod_name;
    $this->loadMatchingObject();

    $this->mod_type = $setup->mod_type;
    $this->_latest  = $setup->mod_version;
    $this->_upgradable = $this->mod_version < $this->_latest;
    $this->_too_new    = $this->mod_version > $this->_latest;
    $this->_configable = is_file("modules/$this->mod_name/configure.php");

    if ($this->_id) {
      $this->_dsns = $setup->getDatasources();
    }

    $this->_dependencies = $setup->dependencies;
    
    if (!$this->_id) {
      $this->mod_ui_order = 1000;
    }
  }

  /**
   * checkModuleFiles
   *
   * @return bool
   */
  function checkModuleFiles(){
    $this->_files_missing = !self::exists($this->mod_name);
    return !$this->_files_missing;
  }

  /**
   * update form fields
   *
   * @return null
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = CAppUI::tr("module-$this->mod_name-court");
  }

  /**
   * Load a module by name
   *
   * @param string $name module name
   *
   * @return object
   */
  function loadByName($name) {
    $this->mod_name = $name;
    return $this->loadMatchingObject();
  }

  /**
   * get the permission module
   *
   * @param int $permType
   *
   * @return bool
   */
  function getPerm($permType) {
    return CPermModule::getPermModule($this->mod_id, $permType);
  }
  
  function getView($permType) {
    return CPermModule::getViewModule($this->mod_id, $permType);
  }

  /**
   * get the update message following mod_version
   *
   * @param CSetup $setup          setup object to check
   * @param bool   $onlyNextUpdate only the next update message ?
   *
   * @return array messages list [version => message]
   */
  function getUpdateMessages(CSetup $setup, $onlyNextUpdate = false) {
    $this->_update_messages = $setup->messages;
    if ($onlyNextUpdate) {
      foreach ($this->_update_messages as $version => $message) {
        if ($version < $this->mod_version) {
          unset($this->_update_messages[$version]);
        }
      }
    }
    return $this->_update_messages;
  }

  /**
   * Checks the View permission on the module
   *
   * @return bool
   */
  function canView() {
    return $this->_canView = $this->getView(PERM_READ);
  }

  /**
   * Checks the Admin permission on the module
   *
   * @return bool
   */
  function canAdmin() {
    return $this->_canEdit = $this->getView(PERM_EDIT);
  }

  /**
   * @see parent::canDo()
   */
  function canDo(){
    if ($this->_can) {
      return $this->_can;
    }

    parent::canDo();

    $this->_can->view  = $this->canView();
    $this->_can->admin = $this->canAdmin();

    // Module view can be shown information
    $this->_can->context = "module $this->_view";
    return $this->_can;
  }

  /**
   * Load the list of visible modules
   *
   * @param bool $shm Use shared memory
   *
   * @return void
   */
  static function loadModules($shm = true) {
    // @todo Experiment, then remove test if no problem
    if ($shm) {
      $modules = SHM::get("modules");
      
      if (!$modules) {
        $module = new self;
        $modules = $module->loadList(null, "mod_ui_order");
        SHM::put("modules", $modules);
      }
    }
    else {
      $module = new self;
      $modules = $module->loadList(null, "mod_ui_order");
    }

    /** @var $module CModule */
    foreach ($modules as &$module) {
      $module->checkModuleFiles();
      self::$installed[$module->mod_name] =& $module;

      if ($module->mod_active == 1) {
        self::$active[$module->mod_name] =& $module;  
      }

      if ($module->mod_ui_active == 1) {
        self::$visible[$module->mod_name] =& $module;
      }

      if ($module->_files_missing) {
        self::$absent[$module->mod_name] =& $module;
      }
    }
  }

  /**
   * Registers a new tab in the list
   *
   * @param string $file     The file to add as a tab
   * @param int    $permType The permission level required
   *
   * @return void
   */
  function registerTab($file, $permType) {
    switch ($permType) {
      case TAB_READ:
        if ($this->canRead()) {
          $this->_tabs[] = $file;
        }
        break;

      case TAB_EDIT:
        if ($this->canEdit()) {
          $this->_tabs[] = $file;
        }
        break;

      case TAB_ADMIN:
        if ($this->canAdmin()) {
          $this->_tabs[] = $file;
        }
        break;

      default:
        // nothing to do
        break;
    }
  }

  /**
   * Returns the $tab if it is valid, the first one from $this->_tabs if not
   *
   * @param string $tab The tab to validate
   *
   * @return mixed
   */
  function getValidTab($tab){
    if (!$this->mod_active) {
      return null;
    }
    
    // Try to access wanted tab
    $tabPath = "./modules/$this->mod_name/$tab.php";
    if (!is_file($tabPath)) {
      return $this->_tabs[0];
    }

    return $tab;
  }

  /**
   * Adds the "Configure" tab
   *
   * @return void
   */
  function addConfigureTab() {
    // Add configure tab if exist
    $configPath = "./modules/$this->mod_name/configure.php";
    if (is_file($configPath) && (CAppUI::$instance->user_type == 1)) {
      $this->registerTab("configure", TAB_ADMIN);
    }
  }

  /**
   * Shows the list of available tabs
   *
   * @return void
   */
  function showTabs() {
    if (!$this->checkActive()) {
      return;
    }
    
    global $uistyle, $tab, $a, $action, $actionType;

    // Try to access wanted tab
    $tabPath = "./modules/$this->mod_name/$tab.php";
    if (!is_file($tabPath)) {
      CAppUI::redirect("m=system&a=access_denied");
    }
    
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

    include_once $tabPath;
  
    $smartyStyle->assign("fintab", true);
    $smartyStyle->display("tabbox.tpl");
    
  }

  /**
   * Shows the "action" page
   *
   * @return void
   */
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

    if (is_file($actionPath)) {
      include_once $actionPath;
    }
  }

  /**
   * Checks if the module is active
   *
   * @return bool
   */
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
   *
   * @param string $moduleName Module name
   *
   * @return bool true if the module exists
   */
  static function exists($moduleName) {
    return is_file("./modules/$moduleName/index.php"); 
  }

  /**
   * Returns all or a named installed module
   *
   * @param string $moduleName Module name
   *
   * @return CModule|CModule[]
   */
  static function getInstalled($moduleName = null) {
    if ($moduleName) {
      return isset(self::$installed[$moduleName]) ? self::$installed[$moduleName] : null;
    }

    return self::$installed;
  }

  /**
   * Returns all or a named active module
   *
   * @param string $moduleName Module name
   *
   * @return CModule|CModule[]
   */
  static function getActive($moduleName = null) {
    if ($moduleName) {
      return isset(self::$active[$moduleName]) ? self::$active[$moduleName] : null;
    }

    return self::$active;
  }
   
  /**
   * Returns all or a named visible module
   *
   * @param string $moduleName Module name
   *
   * @return CModule|CModule[]
   */
  static function getVisible($moduleName = null) {
    if ($moduleName) {
      return isset(self::$visible[$moduleName]) ? self::$visible[$moduleName] : null;
    }

    return self::$visible;
  }

  /**
   * get CanDo object for given installed module,
   *
   * @param string $moduleName Module name
   *
   * @return CCanDo with no permission if module not installed
   */
  static function getCanDo($moduleName) {
    $module = self::getInstalled($moduleName);
    return $module ? $module->canDo() : new CCanDo;
  }

  /**
   * Reorder modules' ranks
   *
   * @return void
   */
  function reorder() {
    /** @var self[] $all_modules */
    $all_modules = $this->loadList(null, "mod_ui_order");

    $i = 1;
    foreach ($all_modules as $_module) {
      $_module->mod_ui_order = $i++;
      $_module->store();
    }
  }

  /**
   * Install a module and reorders the list
   *
   * @return bool
   */
  function install() {
    if ($msg = $this->store()) {
      return false;
    }

    $this->reorder();
    return true;
  }

  function move($dirn) {
    $temp = $this->mod_ui_order;
    if ($dirn == "moveup") {
      $temp--;
      $query = "UPDATE modules SET mod_ui_order = (mod_ui_order+1) WHERE mod_ui_order = $temp";
      $this->_spec->ds->exec($query);
    }
    else if ($dirn == "movedn") {
      $temp++;
      $query = "UPDATE modules SET mod_ui_order = (mod_ui_order-1) WHERE mod_ui_order = $temp";
      $this->_spec->ds->exec($query);
    }
    $query = "UPDATE modules SET mod_ui_order = $temp WHERE mod_id = $this->mod_id";
    $this->_spec->ds->exec($query);

    $this->mod_id = $temp;
    
    $this->reorder();
  }

  /**
   * Upgrade all modules
   *
   * @return void
   */
  static function upgradeAll() {
    $module = new self();

    /** @var self[] $installed */
    $installed = $module->loadList();

    $upgradeables = array();

    foreach ($installed as $_module) {
      $setupClass = "CSetup$_module->mod_name";
      if (!class_exists($setupClass)) {
        continue;
      }

      /** @var CSetup $setup */
      $setup = new $setupClass;
      $_module->compareToSetup($setup);

      if ($_module->_upgradable) {
        $upgradeables[$_module->mod_name] = array(
          "module" => $_module,
          "setup"  => $setup,
        );
      }
    }

    foreach ($upgradeables as $_upgrade) {
      /** @var CModule $_module */
      $_module = $_upgrade["module"];

      /** @var CSetup $_setup */
      $_setup  = $_upgrade["setup"];

      if ($_module->mod_version = $_setup->upgrade($_module->mod_version)) {
        $_module->mod_type = $_setup->mod_type;
        $_module->store();

        if ($_setup->mod_version == $_module->mod_version) {
          CAppUI::setMsg("Installation de '%s' à la version %s", UI_MSG_OK, $_module->mod_name, $_setup->mod_version);
        }
        else {
          CAppUI::setMsg(
            "Installation de '$_module->mod_name' à la version $_module->mod_version sur $_setup->mod_version",
            UI_MSG_WARNING,
            true
          );
        }
      }
      else {
        CAppUI::setMsg("Module '%s' non mis à jour", UI_MSG_WARNING, $_module->mod_name);
      }
    }
  }
}

CModule::loadModules(false);
