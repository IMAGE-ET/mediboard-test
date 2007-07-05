<?php /* $Id: errors.php 1390 2006-12-13 09:55:29Z maskas $ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision: 1390 $
 * @author Thomas Despoix
 */


global $AppUI, $performance, $shm;

$performance["autoload"] = 0;

// Load class paths in shared memory
if ($shm->isReady()) {
  if (null == $classPaths = $shm->get("class-paths")) {
    $AppUI->getAllClasses();
    $classNames = getChildClasses(null);
    foreach($classNames as $className) {
      $class = new ReflectionClass($className);
      $classPaths[$className] = $class->getFileName();
    }
    
    $shm->put("class-paths", $classPaths);
  } 
  
  function __autoload($className) {
    global $classPaths, $performance;
    // Recherche dans les classes de mediboard
    if (array_key_exists($className, $classPaths)) {
      $performance["autoload"]++;
      require_once($classPaths[$className]);
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
  $AppUI->getAllClasses();
}

?>
