<?php /* $Id: errors.php 26 2006-05-04 16:12:16Z Rhum1 $ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision: 26 $
 * @author Thomas Despoix
 * latest version: $HeadURL: https://svn.sourceforge.net/svnroot/mediboard/trunk/includes/errors.php $ 
 */

switch ($dPconfig["shared_memory"]) {
  case "none" :

  /**
   * Returns true if shared memory is available
   */
  function shm_ready() {
    return false;
  }
  
  /**
   * Get a variable from shared memory if cache engine exists
   */
  function shm_get($key) {
    return null;
  }
  
  /**
   * Put a variable into shared memory if cache engine exists
   */
  function shm_put($key, $value) {
    return false;
  }
  
  /**
   * Remove a variable from shared memory if cache engine exists
   */
  function shm_rem($key) {
    return false;
  }

  
  break;
  
  case "eaccelerator" :

  function shm_ready() {
    if (function_exists("eaccelerator_get") and function_exists("eaccelerator_put") and function_exists("eaccelerator_rm")) {
      return true;
    }
    
    return false;
  }
  
  function shm_get($key) {
    global $rootName;
    $key = "$rootName-$key";
  
    if (function_exists("eaccelerator_get")) {
      if ($get = eaccelerator_get($key)) {
        return unserialize($get);
      }
    }
  
    return null;
  }
  
  function shm_put($key, $value) {
    global $rootName;
    $key = "$rootName-$key";
  
    if (function_exists("eaccelerator_put")) {
      return eaccelerator_put($key, serialize($value));
    }
    
    return false;
  }
  
  function shm_rem($key) {
    global $rootName;
    $key = "$rootName-$key";
  
    if (function_exists("eaccelerator_rm")) {
      return eaccelerator_rm($key);
    }
    
    return false;
  }
  break;
  
  default:
    trigger_error("Mode de mémoire partagée non reconnu");
}
?>
