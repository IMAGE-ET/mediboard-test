<?php /* $Id: mbobject.class.php 31 2006-05-05 09:55:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */

class CMbArray {
  /**
   * Compares the content of two arrays
   * @return an associative array with values 
   *   "absent_from_array1"
   *   "absent_from_array2"
   *   "different_values"  
   */
  function compareKeys($array1, $array2) {
    $diff = array();

    foreach ($array1 as $key => $value) {
      if (!array_key_exists($key, $array2)) {
        $diff[$key] = "absent_from_array2";
        continue;
      }

      if ($value != $array2[$key]) {
        $diff[$key] = "different_values";
      }      
    } 

    foreach ($array2 as $key => $value) {
      if (!array_key_exists($key, $array1)) {
        $diff[$key] = "absent_from_array1";
        continue;
      }

      if ($value != $array1[$key]) {
        $diff[$key] = "different_values";
      }      
    }
    
    return $diff; 
  }
  
  /**
   * Returns the value following the given one in cycle mode
   * @param Array $array
   * @param mixed $value
   * @return mixed Next value, false if $value does not exist
   */
  function cycleValue($array, $value) {
    $array = array_unique($array);
    while ($value !== current($array)) {
      next($array);
      if (false === current($array)) {
        trigger_error("value could not be found in array", E_USER_NOTICE);
        return false;
      }
    } 
    
    if (false === $nextValue = next($array)) {
      $nextValue = reset($array);
    }
    
    return $nextValue;
  }
  
  /**
   * Extract a key from an array, returning the value if exists
   * @param array $array The array to explore
   * @param string $name Name of the key to extract
   * @param mixed $default The default value is $key is not found
   * @param bool $mandatory will trigger an warning if value is null 
   */
  function extract(&$array, $key, $default = null, $mandatory = false) {
    $value = mbGetValue(@$array[$key], $default);
    unset($array[$key]);
    if (!$value and $mandatory) {
      $this->trigger_error("mb_field: paramater 'object' missing", E_USER_WARNING);
    }
    return $value;
  }
  
  /**
   * Return a string of XML attributes based on given array key-value pairs 
   */
  function makeXmlAttributes(&$array) {
    $return = array();
    foreach ($array as $key => $value) {
      $value = htmlspecialchars($value);
      $return[] = "$key=\"$value\"";
    }
    return join($return, " ");
  }
}
?>