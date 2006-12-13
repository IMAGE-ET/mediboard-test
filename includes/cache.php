<?php /* $Id: errors.php 26 2006-05-04 16:12:16Z Rhum1 $ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision: 26 $
 * @author Thomas Despoix
 * latest version: $HeadURL: https://svn.sourceforge.net/svnroot/mediboard/trunk/includes/errors.php $ 
 */

/**
 * Get a variable from shared memory if cache engine exists
 */
function shm_get($key) {
  if (function_exists("eaccelerator_get")) {
    return unserialize(eaccelerator_get($key));
  }

  return null;
}

/**
 * Put a variable into shared memory if cache engine exists
 */
function shm_put($key, $value) {
  if (function_exists("eaccelerator_put")) {
    return eaccelerator_put($key, serialize($value));
  }
  
  return false;
}

/**
 * Remove a variable from shared memory if cache engine exists
 */
function shm_rem($key) {
  if (function_exists("eaccelerator_rm")) {
    return eaccelerator_rm($key);
  }
  
  return false;
}

/**
 * Retrive list of variable keys in shared memory
 */
function shm_list() {
  $keys = array();
  
  if (function_exists("eaccelerator_list_keys")) {
    foreach (eaccelerator_list_keys() as $key) {
      $keys[] = trim($key["name"], ":");
    }
  }
  
  return $keys;
}

?>
