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

/**
 * The actual application class
 * Responsibilities:
 *  - application kill
 *  - class management
 *  - file inclusion
 *  - memory and performance
 */
class CApp {
  static $inPeace = false;
  static $encoding = "utf-8";
  static $classPaths = array();
  
  /* 
   * The order of the keys is important (only the first keys 
   * are displayed in the short view of the Firebug console).
   */
  static $performance = array(
    // Performance
    "genere"  => null,
    "memoire" => null,
    "size"    => null,
    "objets"  => 0,
    "ip"      => null,
    
    // Errors
    "error"   => 0,
    "warning" => 0,
    "notice"  => 0,
    
    // Cache
    "cachableCount"  => null,
    "cachableCounts" => null,
    
    // Objects
    "objectCounts" => null,
  );

  /**
   * @var Chronometer Main application chronometer
   */
  static $chrono;
    
  static $handlers;
  
  /**
   * Will trigger an error for logging purpose whenever the application dies unexpectedly
   * 
   * @return void
   */
  static function checkPeace() {
    if (!self::$inPeace) {
      if (!headers_sent()) {
        header("HTTP/1.1 500 Application died unexpectedly");
      }
      
      trigger_error("Application died unexpectedly", E_USER_ERROR);      
    }
  }
  
  /**
   * Make application die properly
   * 
   * @return void
   */
  static function rip() {
    // If the client doesn't support cookies, we destroy its session
    // Sometimes, the cookie is empty while the client support cookies (url auth in IE)
    /*if (empty($_COOKIE)) {
      CSessionHandler::end();
    }*/
    
    self::$inPeace = true;
    die;
  }
  
  /**
   * Apply a ratio multiplicator to current memory limit
   * 
   * @param float $ratio Ratio to apply
   * 
   * @return int Previous memory limit
   */
  static function memoryRatio($ratio) {
    $limit = CMbString::fromDecaSI(ini_get("memory_limit"), "") * $ratio;
    $limit = CMbString::toDecaSI($limit, "");
    return ini_set("memory_limit", $limit);
  }

  /**
   * Set time limit in seconds
   *
   * @param integer $seconds The time limit in seconds
   *
   * @return void
   */
  static function setTimeLimit($seconds) {
    set_time_limit($seconds);
  }

  /**
   * Set memory limit in megabytes
   *
   * @param integer $megabytes The memory limit in megabytes
   *
   * @return string Previous memory limit
   */
  static function setMemoryLimit($megabytes) {
    return ini_set("memory_limit", "{$megabytes}M");
  }
  
  /**
   * Redirect to empty the POST data, 
   * so that it is not posted back when refreshing the page.
   * Use it instead of CApp::rip() directly
   * 
   * @param bool $redirect Try to redirect if true
   * 
   * @return void
   */
  static function emptyPostData($redirect = true){
    if ($redirect && !empty($_POST) && !headers_sent()) {
      CAppUI::redirect(/*CValue::read($_SERVER, "QUERY_STRING")*/);
    }
    self::rip();
  }
  
  /**
   * Outputs JSON data after removing the Output Buffer, with a custom mime type
   * 
   * @param object|array $data     The data to output
   * @param string       $mimeType [optional] The mime type of the data, application/json by default
   * 
   * @return void
   */
  static function json($data, $mimeType = "application/json") {
    ob_clean();
    header("Content-Type: $mimeType");
    echo json_encode($data);
    self::rip();
  }

  /**
   * Fetch an HTML content of a module view, as a HTTP GET call would do
   * Very useful to assemble multiple views
   *
   * @param string $module    The module name or the file path
   * @param string $file      [optional] The file of the module, or null
   * @param array  $arguments [optional] The GET arguments
   *
   * @return string The fetched content
   */
  static function fetch($module, $file = null, $arguments = array()) {
    $save = array();
    foreach ($arguments as $_key => $_value) {
      if (!isset($_GET[$_key])) {
        continue;
      }
      
      $save[$_key] = $_GET[$_key];
    }
    
    foreach ($arguments as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    ob_start();
    if (isset($file)) {
      include "./modules/$module/$file.php";
    }
    else {
      include $module;
    }
    $output = ob_get_clean();
   
    foreach ($save as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    return $output;
  }
  
  /**
   * Get the base application URL
   * 
   * @return string The URL
   */
  static function getBaseUrl(){
    $scheme = "http".(isset($_SERVER["HTTPS"]) ? "s" : "");
    $host = $_SERVER["SERVER_NAME"];
    $port = ($_SERVER["SERVER_PORT"] == 80) ? "" : ":{$_SERVER['SERVER_PORT']}";
    $path = dirname($_SERVER["SCRIPT_NAME"]);
    
    return $scheme."://".$host.$port.$path;
  }
  
  /**
   * Include all the classes of the framework and modules
   * 
   * @return void
   */
  static function getAllClasses() {
    $rootDir = CAppUI::conf("root_dir");
    
    // Ordered paths
    $dirs = array(
       // Require all global classes
      "classes/*/*.class.php",
      "classes/*.class.php", 
      "*/*/*.class.php",
      // Require all modules classes
      "modules/*/classes/*.class.php",
      // Require all modules setups 
      "modules/*/setup.php",
    );
    
    // Actual requires
    foreach ($dirs as $dir) {
      $files = glob("$rootDir/$dir");
      foreach ($files as $fileName) {
        include_once $fileName;
      }
    }
  }

  /**
   * Return all child classes of a given class having given properties
   *
   * @param string $parent        [optional] Parent class
   * @param array  $properties    [optional] No property checking if empty
   * @param bool   $active_module [optional] If true, filter on active modules
   *
   * @return array Class names
   * @todo Default parent class should probably be CModelObject
   */
  static function getChildClasses($parent = "CMbObject", $properties = array(), $active_module = false) {
    $childclasses = SHM::get("child-classes");

    // Do not cache when we want all the classes
    if ($parent && empty($properties) && isset($childclasses[$parent][$active_module])) {
      return $childclasses[$parent][$active_module];
    }
    
    self::getAllClasses();
    
    $classes = get_declared_classes();
    foreach ($classes as $key => $class) {
      // Filter on parent class
      if ($parent && !is_subclass_of($class, $parent)) {
        unset($classes[$key]);
        continue;
      }
  
      // Filter on properties
      foreach ($properties as $prop) {
        if (!array_key_exists($prop, get_class_vars($class))) {
          unset($classes[$key]);
        }
      }
      
      // Filter on active module
      if ($active_module) {
        $object = new $class; 
        if (!isset($object->_ref_module)) {
          unset($classes[$key]);
        }
      }
    }
    
    sort($classes);
    
    // Caching
    if ($parent && empty($properties)) {
      $childclasses[$parent][$active_module] = $classes;
      SHM::put("child-classes", $childclasses);
    }
    
    return $classes;
  }
  
  /**
   * Return all CMbObject child classes
   * 
   * @param array $properties [optional] Filter on properties
   * @param array &$instances [optional] If not null, retrieve an array of all object instances
   * 
   * @return array Class names
   */
  static function getMbClasses($properties = array(), &$instances = null) {
    $classes = self::getChildClasses("CMbObject", $properties);
    
    foreach ($classes as $key => $class) {
      // In case we removed a class and it's still in the cache
      if (!class_exists($class, true)) {
        unset($classes[$key]);
        continue;
      }
      
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
     
      // Instanciated class?
      // @todo All class should be instanciable 
      if (!$object->_class) {
        unset($classes[$key]);
        continue;
      }
      
      $instances[$class] = $object;
    }
    
    return $classes;
  }
  
  /**
   * Return all storable CMbObject classes which module is installed
   *
   * @param array $classes [optional] Restrain to given classes
   * 
   * @return array Class names
   */
  static function getInstalledClasses($classes = array()) {
    if (empty($classes)) {
      $classes = self::getMbClasses();
    }
    
    foreach ($classes as $key => $class) {
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
      
      // Installed module ?
      if ($object->_ref_module === null) {
        unset($classes[$key]);
        continue;
      }
  
      // Storable class ?
      if (!$object->_spec->table) {
        unset($classes[$key]);
        continue;
      }
    }
    
    return $classes;
  }
  
  /**
   * Group installed classes by module names
   * 
   * @param array $classes Class names
   * 
   * @return array Array with module names as key and class names as values
   */
  static function groupClassesByModule($classes) {
    $grouped = array();
    foreach ($classes as $class) {
      $object = new $class;
      if ($module = $object->_ref_module) {
        $grouped[$module->mod_name][] = $class;
      }
    }
    return $grouped;
  }
  
  /**
   * Staticly build index handlers array
   * 
   * @return void
   */
  public static final function makeHandlers() {
    if (is_array(self::$handlers)) {
      return;
    }
    
    // Static initialisations
    self::$handlers = array();
    foreach (CAppUI::conf("index_handlers") as $handler => $active) {
      if ($active) {
        self::$handlers[$handler] = new $handler;
      }
    }
  }
  
  /**
   * Subject notification mechanism 
   * TODO Implement to factorize 
   *   on[Before|After][Store|Merge|Delete]()
   *   which have to get back de CPersistantObject layer
   * 
   * @param string $message The notification type
   * 
   * @return void
   */
  static function notify($message) {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $handler) {
      try {
        call_user_func(array($handler, "on$message"));
      } 
      catch (Exception $e) {
        CAppUI::setMsg($e, UI_MSG_ERROR);
      }
    }
  }

  /**
   * Prepare performance data to be displayed
   *
   * @return void
   */
  static function preparePerformance(){
    arsort(CMbObject::$cachableCounts);
    arsort(CMbObject::$objectCounts);
    arsort(self::$performance["autoload"]);

    self::$performance["genere"]         = number_format(self::$chrono->total, 3);
    self::$performance["memoire"]        = CHTMLResourceLoader::getOutputMemory();
    self::$performance["objets"]         = CMbObject::$objectCount;
    self::$performance["cachableCount"]  = array_sum(CMbObject::$cachableCounts);
    self::$performance["cachableCounts"] = CMbObject::$cachableCounts;
    self::$performance["objectCounts"]   = CMbObject::$objectCounts;
    self::$performance["ip"]             = $_SERVER["SERVER_ADDR"];

    self::$performance["size"] = CHTMLResourceLoader::getOutputLength();
    self::$performance["ccam"] = array (
      "cacheCount" => class_exists("CCodeCCAM") ? CCodeCCAM::$cacheCount : 0,
      "useCount"   => class_exists("CCodeCCAM") ? CCodeCCAM::$useCount : 0
    );

    // Data sources performance
    foreach (CSQLDataSource::$dataSources as $dsn => $ds) {
      if (!$ds) {
        continue;
      }

      $chrono      = $ds->chrono;
      $chronoFetch = $ds->chronoFetch;

      self::$performance["dataSources"][$dsn] = array(
        "count"      => $chrono->nbSteps,
        "time"       => $chrono->total,
        "countFetch" => $chronoFetch->nbSteps,
        "timeFetch"  => $chronoFetch->total,
      );
    }
  }
}
