<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $performance, $shm;
$performance["autoload"] = 0;

// Load class paths in shared memory
if ($shm->isReady()) {
  if (null == $classPaths = $shm->get("class-paths")) {
    CAppUI::getAllClasses();
    $classNames = getChildClasses(null);
    foreach ($classNames as $className) {
      $class = new ReflectionClass($className);
      $classPaths[$className] = $class->getFileName();
    }
    
    $shm->put("class-paths", $classPaths);
  }

  function __autoload($className) {
    global $classPaths, $performance;
      
    // Recherche dans les classes de mediboard
    if (isset($classPaths[$className])) {
      $performance["autoload"]++;
      require($classPaths[$className]);
      return;
    } 
    
    // Recherche dans les classes de ezComponent
    if (@include "ezc/Base/base.php") {
      ezcBase::autoload( $className );
    }
  }
}
// Load all classes normally
else {
  CAppUI::getAllClasses();
}

?>
