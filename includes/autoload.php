<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $performance;
$performance["autoload"] = 0;

// Load class paths in shared memory
if (null == $classPaths = SHM::get("class-paths")) {
  updateClassPathCache();
}

/** Updates the PHP classes paths cache */
function updateClassPathCache(){
  $classNames = CApp::getChildClasses(null);
  foreach ($classNames as $className) {
    $class = new ReflectionClass($className);
    $classPaths[$className] = $class->getFileName();
  }
  
  SHM::put("class-paths", $classPaths);
}

function mb_autoload($className) {
  global $classPaths, $performance;

  if (isset($classPaths[$className]) && file_exists($classPaths[$className])) {
    $performance["autoload"]++;
    return require($classPaths[$className]);
  }
  else {
    updateClassPathCache();
  }
  return true;
}

if (function_exists("spl_autoload_register")) {
  spl_autoload_register("mb_autoload");
}
else {
  function __autoload($className) {
    return mb_autoload($className);
  }
}
