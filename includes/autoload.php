<?php 
/**
 * Autoload strategies
 *
 * PHP version 5.1.x+
 *  
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

CApp::$performance["autoload"] = 0;

CApp::$classPaths = SHM::get("class-paths");

/**
 * Mediboard class autoloader
 * 
 * @param string $class Class to be loaded
 * 
 * @return bool
 */
function mbAutoload($class) {
  $file_exists = false;
  
  // Entry already in cache
  if (isset(CApp::$classPaths[$class])) {
    // The class is known to not be in MB
    if (CApp::$classPaths[$class] === false) {
      return false;
    }
    
    // Load it if we can
    if ($file_exists = file_exists(CApp::$classPaths[$class])) {
      CApp::$performance["autoload"]++;
      return include_once CApp::$classPaths[$class];
    }
  }
  
  // File moved ?
  if (!$file_exists) {
    unset(CApp::$classPaths[$class]);
  }
  
  // CSetup* class
  if (preg_match("/^CSetup.+/", $class)) {
    $dirs = array("modules/*/setup.php");
  }
  
  // Other class
  else {
    $dirs = array(
      "classes/$class.class.php", 
      "classes/*/$class.class.php",
      "mobile/*/$class.class.php",
      "modules/*/classes/$class.class.php",
      "modules/*/classes/*/$class.class.php",
      "modules/*/classes/*/*/$class.class.php",
    );
  }
  
  $rootDir = CAppUI::conf("root_dir");
  
  foreach ($dirs as $dir) {
    $files = glob("$rootDir/$dir");
    foreach ($files as $filename) {
      include_once $filename;
    }
  }
  
  $mb_class = true;
  
  // The class was found
  if (class_exists($class, false) || interface_exists($class, false)) {
    $reflection = new ReflectionClass($class);
    CApp::$classPaths[$class] = $reflection->getFileName();
  }
  
  // Class not found, it is not in MB
  else {
    CApp::$classPaths[$class] = false;
    $mb_class = false;
  }
  
  SHM::put("class-paths", CApp::$classPaths);
  
  return $mb_class;
}

if (function_exists("spl_autoload_register")) {
  spl_autoload_register("mbAutoload");
}
else {
  /**
   * Autoload magic function redefinition
   * 
   * @param string $class Class to be loaded
   * 
   * @return bool
   */
  function __autoload($class) {
    return mbAutoload($class);
  }
}
