<?php 
/**
 * Autoload strategies
 *  
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

CApp::$performance["autoloadCount"] = 0;
CApp::$performance["autoload"] = array();

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
  $time = microtime(true);
  
  // Entry already in cache
  if (isset(CApp::$classPaths[$class])) {
    // The class is known to not be in MB
    if (CApp::$classPaths[$class] === false) {
      return false;
    }
    
    // Load it if we can
    if ($file_exists = file_exists(CApp::$classPaths[$class])) {
      CApp::$performance["autoloadCount"]++;
      return include_once CApp::$classPaths[$class];
    }
  }
  
  // File moved ?
  if (!$file_exists) {
    unset(CApp::$classPaths[$class]);
  }
  
  // CSetup* class
  if (preg_match('/^CSetup(.+)$/', $class, $matches)) {
    $dirs = array(
      "modules/$matches[1]/setup.php",
    );
  }
  
  // Other class
  else {
    $class_file = $class;
    $suffix = ".class";

    // Namespaced class
    if (strpos($class_file, "\\") !== false) {
      $namespace = explode("\\", $class_file);

      // Mediboard class
      if ($namespace[0] === "Mediboard") {
        array_shift($namespace);
        $class_file = implode("/", $namespace);
      }

      // Vendor class
      else {
        $class_file = "vendor/".implode("/", $namespace);
        $suffix = "";
      }
    }

    $class_file .= $suffix;

    $dirs = array(
      "classes/$class_file.php",
      "classes/*/$class_file.php",
      "mobile/*/$class_file.php",
      "modules/*/classes/$class_file.php",
      "modules/*/classes/*/$class_file.php",
      "modules/*/classes/*/*/$class_file.php",
      "install/classes/$class_file.php",
    );
  }
  
  $rootDir = CAppUI::conf("root_dir");
  
  $class_path = false;
  
  foreach ($dirs as $dir) {
    $files = glob("$rootDir/$dir");
    
    foreach ($files as $filename) {
      include_once $filename;
  
      // The class was found
      if (class_exists($class, false) || interface_exists($class, false)) {
        $class_path = $filename;
        break 2;
      }
    }
  }
  
  // Class not found, it is not in MB
  CApp::$classPaths[$class] = $class_path;
  
  SHM::put("class-paths", CApp::$classPaths);
  
  CApp::$performance["autoload"][$class] = (microtime(true) - $time) * 1000;
  
  return $class_path !== false;
}

spl_autoload_register("mbAutoload");