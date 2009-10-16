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
  CAppUI::getAllClasses();
  $classNames = getChildClasses(null);
  foreach ($classNames as $className) {
    $class = new ReflectionClass($className);
    $classPaths[$className] = $class->getFileName();
  }
  
  SHM::put("class-paths", $classPaths);
}

function __autoload($className) {
  global $classPaths, $performance;
    
  // Recherche dans les classes de mediboard
  if (isset($classPaths[$className])) {
    $performance["autoload"]++;
    return require($classPaths[$className]);
  } 
  
  // Recherche dans les classes de ezComponent
  if (@include "ezc/Base/base.php") {
    ezcBase::autoload( $className );
  }
}
