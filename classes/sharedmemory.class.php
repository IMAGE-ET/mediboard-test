<?php /* CLASSES $Id: chrono.class.php 16 2006-05-04 14:54:07Z MyttO $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 16 $
 */

/**
 * Default Shared Memory class with no shared memory behaviour
 */
class SharedMemory {
    
  /**
   * Returns true if shared memory is available
   */
  function isReady() {
    return false;
  }
  
  /**
   * Get a variable from shared memory
   */
  function get($key) {
    return null;
  }
  
  /**
   * Put a variable into shared memory
   */
  function put($key, $value) {
    return false;
  }
  
  /**
   * Remove a variable from shared memory
   */
  function rem($key) {
    return false;
  }
}

class EAcceleratorSharedMemory extends SharedMemory {
  
  function isReady() {
    if (function_exists("eaccelerator_get") and function_exists("eaccelerator_put") and function_exists("eaccelerator_rm")) {
      return true;
    }
    
    return false;
  }
  
  function get($key) {
    global $rootName;
    $key = "$rootName-$key";
  
    if (function_exists("eaccelerator_get")) {
      if ($get = eaccelerator_get($key)) {
        return unserialize($get);
      }
    }
  
    return null;
  }
  
  function put($key, $value) {
    global $rootName;
    $key = "$rootName-$key";
  
    if (function_exists("eaccelerator_put")) {
      return eaccelerator_put($key, serialize($value));
    }
    
    return false;
  }
  
  function rem($key) {
    global $rootName;
    $key = "$rootName-$key";
  
    if (function_exists("eaccelerator_rm")) {
      return eaccelerator_rm($key);
    }
    
    return false;
  }
}

// Shared Memory instance factory
global $dPconfig;
switch ($dPconfig["shared_memory"]) {
  case "none" : 
  $shm = new SharedMemory;
  break;
    
  case "eaccelerator" :
  $shm = new EAcceleratorSharedMemory;
  break;
  
  default:
  trigger_error("Mode de mémoire partagée non reconnu", E_USER_ERROR);
  $shm = new SharedMemory;
}

?>