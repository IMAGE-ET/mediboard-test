<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

abstract class CMbArray {
  /**
   * Compares the content of two arrays
   * @return an associative array with values 
   *   "absent_from_array1"
   *   "absent_from_array2"
   *   "different_values"  
   */
  static function compareKeys($array1, $array2) {
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
   * Compute recursively the associative difference between two arrays
   * Function is not commutative, as first array is the reference
   * @param array $array1
   * @param array $array2
   * @return array The difference
   */
  static function diffRecursive($array1, $array2) {
	  foreach ($array1 as $key => $value) {
	    // Array value
	    if (is_array($value)) {
	      if (!isset($array2[$key])) {
	        $difference[$key] = $value;
	      }
	      elseif(!is_array($array2[$key])) {
	        $difference[$key] = $value;
	      } 
	      else {
	        if ($new_diff = self::diffRecursive($value, $array2[$key])) {
	          $difference[$key] = $new_diff;
	        }
	      }
	    }
	    
	    // scalar value
	    elseif (isset($value)) {
		    if (!isset($array2[$key]) || $array2[$key] != $value) {
		      $difference[$key] = $value;
		    }
	    }

	    else {
	      if (!array_key_exists($key, $array2) || $array2[$key]) {
		      $difference[$key] = $value;
	      }
	    }
	  }
	  
	  return isset($difference) ? $difference : false;
	}
  
	/**
	 * Remove all occurences of given value in array
	 * @param mixed $needle Value to remove
	 * @param array $haystack Array to alter
	 * @return int Occurences count
	 **/
	static function removeValue($needle, &$haystack) {
	  $count = 0;
	  while (($key = array_search($needle,  $haystack)) !== false) {
	    unset($haystack[$key]);
	    $count++;
	  }
	  return $count;
	}
	
  /**
   * DEPRECATED ALIAS TO BUILT IN range() TO REMOVE
   * 
   * Build and array with ranged values
   * @param str The string to split out
   */
	static function createRange($min, $max, $key_as_value = false, $step = 1){
    $range = range($min, $max, $step);
	  if ($key_as_value) {
	    $tmp_range = array();
      foreach ($range as $n) {
	      $tmp_range[$n] = $n;
	    }
	    $range = $tmp_range;
    }
    return $range;
	}
	
	/**
	 * Get the previous and next key 
	 * @param $arr The array to seek in
	 * @param $key The target key
	 * @return array Previous and next key in an array, null if unavailable
	 */
	static function getPrevNextKeys($arr, $key){
	  $keys = array_keys($arr);
	  $keyIndexes = array_flip($keys);
	  
	  $return = array();
	  if (isset($keys[$keyIndexes[$key]-1])) {
	    $return["prev"] = $keys[$keyIndexes[$key]-1];
	  }else{
	    $return["prev"] = null;
	  }
	  
	  if(isset($keys[$keyIndexes[$key]+1])) {
	    $return["next"] = $keys[$keyIndexes[$key]+1];
	  }else{
	    $return["next"] = null;
	  }
	  
	  return $return;
	}
	
	/**
	 * Merge recursively two array
	 * @param array $paArray1 First array
	 * @param array $paArray2 The array to be merged
	 * @return array The merge result
	 */
	static function mergeRecursive($paArray1, $paArray2) {
	  if (!is_array($paArray1) || !is_array($paArray2)) { 
      return $paArray2;
	  }
	
	  foreach ($paArray2 as $sKey2 => $sValue2) {
	    $paArray1[$sKey2] = CMbArray::mergeRecursive(@$paArray1[$sKey2], $sValue2);
	  }
	  
	  return $paArray1;
	}

	/**
	 * Alternative to array_merge that always preserves keys
	 * @param array ... Any number of arrays to merge
	 * @return array The merge result
	 */
	static function mergeKeys(){
    $args = func_get_args();
    $result = array();
    foreach($args as $array){
      foreach($array as $key=>$value){
        $result[$key] = $value;
      }
    }
    return $result;
  }
  
  
  /**
   * Returns the value following the given one in cycle mode
   * @param Array $array
   * @param mixed $value
   * @return mixed Next value, false if $value does not exist
   */
  static function cycleValue($array, $value) {
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
  static function get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
  }
  
  /**
   * Returns the first value of the array that isset, from keys
   * @param array $array The array to explore
   * @param array $keys The keys to read
   * @param mixed $default The default value no value is found
   */
  static function first($array, $keys, $default = null) {
  	foreach($keys as $key)
      if (isset($array[$key])) return $array[$key];
      
    return $default;
  }
  
  /**
   * Extract a key from an array, returning the value if exists
   * @param array $array The array to explore
   * @param string $name Name of the key to extract
   * @param mixed $default The default value is $key is not found
   * @param bool $mandatory will trigger an warning if value is null 
   */
  static function extract(&$array, $key, $default = null, $mandatory = false) {
    // Should not use isset
    if (!array_key_exists($key, $array)) {
      if ($mandatory) {
        trigger_error("Could not extract '$key' index in array", E_USER_WARNING);
      }
      return $default;
    }
    
    $value = $array[$key];
    unset($array[$key]);
    return $value;
  }
  
  /**
   * Give a default value to key if key is not set
   * @param array $array the array to alter
   * @param int|string $key The key to check
   * @param mixed $value The default value if key is not set
   */
  static function defaultValue(&$array, $key, $value) {
    // Should not use isset
    if (!array_key_exists($key, $array)) {
      $array[$key] = $value;
    }
  }
  
  /**
   * Return a string of XML attributes based on given array key-value pairs 
   * @param array $array The source array
   * @return string String attributes  like 'key1="value1" ... keyN="valueN"'
   **/
  static function makeXmlAttributes($array) {
    $return = '';
    foreach ($array as $key => $value) {
      if ($value !== null) {
        $value = trim(htmlspecialchars($value));
        $return .= "$key=\"$value\" ";
      }
    }
    return $return;
  }
  
  /**
   * Pluck (collect) given key or attribute name of each value
   * whether the values are arrays or objects.
   * Preserves indexes
   * @param object|array $array The array or object to pluck
   * @param mixed $key The key or attribute name 
   * @return array All plucked values
   */
  static function pluck($array, $name) {
    if (!is_array($array)) return null;
    $values = array(); 
    foreach ($array as $index => $value) {
      if (is_object($value)) {
        $value = get_object_vars($value);
      }
      
      if (!is_array($value)) {
        trigger_error("Value at index '$index' is neither an array nor an object", E_USER_WARNING);
        continue;
      }
      
      if (!array_key_exists($name, $value)) {
        trigger_error("Value at index '$index' can't access to '$name' field", E_USER_WARNING);
        continue;
      }
      
      $values[$index] = $value[$name];
    }
    
    return $values;    
  }
  
  /**
   * Create an array with filtered keys based on having given prefix
   * @param $array array The array to filter
   * @param $prefix string The prefix that has to start key strings
   * @return array The filtered array 
   */
  static function filterPrefix($array, $prefix) {
    $values = array();
    foreach ($array as $key => $value) {
      if (strpos($key, $prefix) === 0) {
        $values[$key] = $value;
      }
    }
    return $values;
  }
}
?>