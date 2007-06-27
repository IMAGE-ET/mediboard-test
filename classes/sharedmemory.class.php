<?php /* $Id$ */


/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 16 $
 */

// Use $dPconfig for both application and install wizard to use it
global $dPconfig;
require_once $dPconfig["root_dir"]. "/classes/mbpath.class.php";

/**
 * Default Shared Memory class with no shared memory behaviour
 */
class SharedMemory {
  var $dir = null;
  
  function __construct() {
    global $dPconfig;
    $this->dir = $dPconfig["root_dir"]."/tmp/shared/";
  }
  
  /**
   * Returns true if shared memory is available
   */
  function isReady() {
     return CMbPath::forceDir($this->dir);
  }
  
  /**
   * Get a variable from shared memory
   */
  function get($key) {
    return unserialize(file_get_contents($this->dir.$key));
  }
  
  /**
   * Put a variable into shared memory
   */
  function put($key, $value) {
     file_put_contents($this->dir.$key, serialize($value)); 
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