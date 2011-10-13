<?php /* $Id: ui.class.php 8520 2010-04-09 14:27:59Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 8520 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The true application class
 */
class CApp {
  static $inPeace = false;
  static $encoding = "utf-8";
  
  /* 
   * The order of the keys is important (only the first keys 
   * are displayed in the short view of the Firebug console).
   */
  static $performance = array(
    // Performance
    "genere" => null,
    "memoire" => null,
    "size" => null,
    "objets" => 0,
    
    // Errors
    "error" => 0,
    "warning" => 0,
    "notice" => 0,
    
    // Cache
    "cachableCount" => null,
    "cachableCounts" => null,
    
    // Objects
    "objectCounts" => null,
  );
    
  /**
   * Will trigger an error for logging purpose whenever the application dies unexpectedly
   */
  static function checkPeace() {
    if (!self::$inPeace) {
      header("HTTP/1.1 500 Application died unexpectedly");
      trigger_error("Application died unexpectedly", E_USER_ERROR);      
    }
  }
  
  /**
   * Make application die properly
   */
  static function rip() {
    // If the client doesn't support cookies, we destroy its session
    if (empty($_COOKIE)) {
      session_unset();
      @session_destroy(); // Escaped because of an unknown error
    }
    
    self::$inPeace = true;
    die;
  }
  
  /**
   * Changes to memory limit to $ratio * [current limit]
   * @param float $ratio
   * @return Old memory limit
   */
  static function memoryRatio($ratio) {
    $limit = CMbString::fromDecaSI(ini_get("memory_limit"), "") * $ratio;
    $limit = CMbString::toDecaSI($limit, "");
    return ini_set("memory_limit", $limit);
  }
  
  /**
   * This will make a redirect to empty the POST data, so 
   * that it is not posted back when refreshing the page.
   * Use it instead of CApp::rip() directly
   */
  static function emptyPostData($redirect = true){
    if ($redirect && !empty($_POST) && !headers_sent()) {
      CAppUI::redirect(/*CValue::read($_SERVER, "QUERY_STRING")*/);
    }
    self::rip();
  }
  
  /**
   * Outputs JSON data after removing the Output Buffer, with a custom mime type
   * @param object $data The data to output
   * @param string $mimeType [optional] The mime type of the data, application/json by default
   * @return void
   */
  static function json($data, $mimeType = "application/json") {
    ob_clean();
    header("Content-Type: $mimeType");
    echo json_encode($data);
    self::rip();
  }
  
  /**
   * 
   * @param object $module The module name or the file path
   * @param object $file [optional] The file of the module, or null
   * @param object $arguments [optional] The GET arguments
   * @return string The fetched content
   */
  static function fetch($module, $file = null, $arguments = array()) {
    $save = array();
    foreach($arguments as $_key => $_value) {
      if (!isset($_GET[$_key])) continue;
      $save[$_key] = $_GET[$_key];
    }
    
    foreach($arguments as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    ob_start();
    if (isset($file)) {
      include("./modules/$module/$file.php");
    }
    else {
      include($module);
    }
    $output = ob_get_clean();
   
    foreach($save as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    return $output;
  }
  
  static function getBaseUrl(){
    $scheme = "http".(isset($_SERVER["HTTPS"]) ? "s" : "");
    $host = $_SERVER["SERVER_NAME"];
    $port = ($_SERVER["SERVER_PORT"] == 80) ? "" : ":{$_SERVER['SERVER_PORT']}";
    $path = dirname($_SERVER["SCRIPT_NAME"]);
    
    return $scheme."://".$host.$port.$path;
  }
  
  /**
   * Includes all the classes of the framework
   * @return void
   */
  static function getAllClasses() {
    $rootDir = CAppUI::conf("root_dir");
    $dirs = array(
      "classes/*/*.class.php", // Require all global classes
      "classes/*.class.php", 
      "*/*/*.class.php",
      "modules/*/classes/*.class.php", // Require all modules classes
      "modules/*/setup.php", // Require all modules setups 
    );
    
    foreach ($dirs as $dir) {
      $files = glob("$rootDir/$dir");
      foreach ($files as $fileName) {
        require_once($fileName);
      }
    }
  }
  
  /**
   * Return all child classe of a given class havin given properties
   * @param array $properties No property checking if empty
   * @return array
   */
  static function getChildClasses($parent = "CMbObject", $properties = array(), $active_module = false) {
    $childclasses = SHM::get("child-classes");
    
    // Do not cache when we want all the classes
    if ($parent && empty($properties) && isset($childclasses[$parent][$active_module])) {
      return $childclasses[$parent][$active_module];
    }
    
    self::getAllClasses();
    
    $listClasses = get_declared_classes();
    foreach ($listClasses as $key => $class) {
      if ($parent and !is_subclass_of($class, $parent)) {
        unset($listClasses[$key]);
        continue;
      }
  
      foreach($properties as $prop) {
        if (!array_key_exists($prop, get_class_vars($class))) {
          unset($listClasses[$key]);
        }
      }
      
      if ($active_module) {
        $object = new $class; 
        if (!isset($object->_ref_module)) {
          unset($listClasses[$key]);
        }
      }
    }
    
    sort($listClasses);
    
    if ($parent && empty($properties)) {
      $childclasses[$parent][$active_module] = $listClasses;
      SHM::put("child-classes", $childclasses);
    }
    
    return $listClasses;
  }
  
  /**
   * Return all CMbObject child classes
   * @param array $properties
   * @return array
   */
  static function getMbClasses($properties = array(), &$instances = null) {
    $classes = self::getChildClasses("CMbObject", $properties);
    foreach ($classes as $key => $class) {
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
      
      // Classe instanci�e ?
      if (!$object->_class) {
        unset($classes[$key]);
        continue;
      }
      
      $instances[$class] = $object;
    }
    
    return $classes;
  }
  
  /**
   * Return all storable classes which module is installed
   * @param array $properties
   * @return array
   */
  static function getInstalledClasses($properties = array(), $classes = array()) {
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
}
?>