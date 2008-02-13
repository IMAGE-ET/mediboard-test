<?php /* $Id$ */

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
	static function createRange($min, $max, $cleasvalue = false, $step = 1){
	  if($min>=$max) {
	    return false;
	  }
	  $aTemp = array();
	  while($min<=$max){
	    if($cleasvalue){
	      $aTemp[$min] = $min;
	    }else{
	      $aTemp[] = $min;
	    }
	    $min += $step;
	  }
	  return $aTemp;
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
	 * @return arrau The merge result
	 */
	static function mergeRecursive($paArray1, $paArray2) {
	  if (!is_array($paArray1) or !is_array($paArray2)) { 
	     return $paArray2;
	  }
	
	  foreach ($paArray2 AS $sKey2 => $sValue2) {
	    $paArray1[$sKey2] = CMbArray::mergeRecursive(@$paArray1[$sKey2], $sValue2);
	  }
	   
	  return $paArray1;
	}
  
  /**
   * DEPRECATED ALIAS TO BUILT IN str_split() TO REMOVE
   * 
   * Build and array with string chars as values
   * @param str The string to split out
   */
	static function fromString($str) {
	  $array = array();
	  for ($i = 0; $i < strlen($str); $i++) {
	    $array[] = $str[$i];
	  }
	  return $array;
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
   * Extract a key from an array, returning the value if exists
   * @param array $array The array to explore
   * @param string $name Name of the key to extract
   * @param mixed $default The default value is $key is not found
   * @param bool $mandatory will trigger an warning if value is null 
   */
  static function extract(&$array, $key, $default = null, $mandatory = false) {
    if (!isset($array[$key])) {
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
    if (!isset($array[$key])) {
      $array[$key] = $value;
    }
  }
  
  /**
   * Return a string of XML attributes based on given array key-value pairs 
   * @param array $array The source array
   * @return string String attributes  like 'key1="value1" ... keyN="valueN"'
   **/
  static function makeXmlAttributes($array) {
    $return = array();
    foreach ($array as $key => $value) {
      if ($value !== null) {
      $value = trim(htmlspecialchars($value));
      $return[] = "$key=\"$value\"";
      }
    }
    return join($return, " ");
  }
  
  /**
   * Create an array of given size filled with given value
   * @param mixed $value Value to fill in
   * @param int $size Size of the built array
   * @return array The built array
   */
  static function fillValues($value, $size) {
    $array = array();
    for ($i = 0; $i < $size; ++$i) {
      $array[] = $value;
    }
    
    return $array;
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
  
  
}
?>