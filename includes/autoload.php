<?php /* $Id: errors.php 1390 2006-12-13 09:55:29Z maskas $ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision: 1390 $
 * @author Thomas Despoix
 */

$performance["autoload"] = 0;

// Load class paths in shared memory
if (shm_ready()) {

  if (null == $classPaths = shm_get("class-paths")) {
    $AppUI->getAllClasses();
    $classNames = getChildClasses(null);
    foreach($classNames as $className) {
      $class = new ReflectionClass($className);
      $classPaths[$className] = $class->getFileName();
    }
    
    shm_put("class-paths", $classPaths);
  } 
  
  function __autoload($className) {
    global $classPaths, $performance;
    if (array_key_exists($className, $classPaths)) {
      $performance["autoload"]++;
      require_once($classPaths[$className]);
    }
  }
}
// Load all classes normally
else {
  $AppUI->getAllClasses();
}

?>
