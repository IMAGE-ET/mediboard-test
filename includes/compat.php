<?php 
/**
 * Compat functions emulations
 *
 * PHP version 5.1.x+
 *  
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */


if (!function_exists('array_diff_key')) {
  /**
   * Computes the difference of arrays using keys for comparison 
   * (PHP 5 >= 5.1.0)
   * 
   * @return array The difference array, false on error
   * @link http://php.net/array_diff_key
   */
  function array_diff_key() {
    $argCount  = func_num_args();

    if ($argCount < 2) {
      return false;
    }
    
    $argValues  = func_get_args();
    foreach ($argValues as $argParam) {
      if (!is_array($argParam)) {
        return false;
      }
    }
    
    $valuesDiff = array();
    foreach ($argValues[0] as $valueKey => $valueData) {
      for ($i = 1; $i < $argCount; $i++) {
        if (isset($argValues[$i][$valueKey])) {
          continue 2;
        }
      }
      $valuesDiff[$valueKey] = $valueData;
    }
		
    return $valuesDiff;
  }
}

/**
 * Recursively applies a function to values of an array
 * 
 * @param string $function Callback to apply
 * @param array  $array    Array to apply callback on
 * 
 * @return arrray
 */
function array_map_recursive($function, $array) {
	// Recursion closure
	if (!is_array($array)) {
		return call_user_func($function, $array);
	}
	
	// Rercursive call
	$result = array();
  foreach ($array as $key => $value ) {
    $result[$key] = array_map_recursive($function, $value);
  }
	
  return $result;
}

/**
 * Checks recursively if a value exists in an array
 * 
 * @param mixed $needle   The searched value.
 * @param array $haystack The array.
 * @param bool  $strict   If true also check value types
 * 
 * @return true if needle is found in the array, false otherwise. 
 */
function in_array_recursive($needle, $haystack, $strict = false) {
  if (in_array($needle, $haystack, $strict)) {
    return true;
  }
  
  foreach ($haystack as $v) {
    if (is_array($v) && in_array_recursive($needle, $v, $strict)) {
      return true;
    }
  }
  
  return false;
}

if (!function_exists('array_replace_recursive')) {
  /**
   * Array recursive replace recurse closure
   * 
   * @param object $array  Merge host array
   * @param object $array1 Merged array
   * 
   * @return array
   * @link  http://php.net/array_replace_recursive
   */
  function array_replace_recursive__recurse($array, $array1) {
    foreach ($array1 as $key => $value) {
      // create new key in $array, if it is empty or not an array
      if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
        $array[$key] = array();
      }

      // overwrite the value in the base array
      if (is_array($value)) {
        $value = array_replace_recursive__recurse($array[$key], $value);
      }
      $array[$key] = $value;
    }
    return $array;
  }
  
  /**
   * Replaces elements from passed arrays into the first array recursively
   * (PHP 5 >= 5.3.0)
   * 
   * @param object $array  Merge host array
   * @param object $array1 Merged array
   * 
   * @return array
   * @link   http://php.net/array_replace_recursive
   */
  function array_replace_recursive($array, $array1) {
    // handle the arguments, merge one by one
    $args = func_get_args();
    $array = $args[0];
    if (!is_array($array)) {
      return $array;
    }
    for ($i = 1; $i < count($args); $i++) {
      if (is_array($args[$i])) {
        $array = array_replace_recursive__recurse($array, $args[$i]);
      }
    }
    return $array;
  }
}

if (!function_exists('array_fill_keys')) {
  /**
   * Fill an array with values, specifying keys
   * 
   * @param array $keys  Keys to fill values
   * @param mixed $value Filling value
   * 
   * @return array Filled array
   * @link   http://php.net/array_fill_keys
   */
	function array_fill_keys($keys, $value) {
    return array_combine($keys, array_fill(0, count($keys), $value));
  }
}

if (!function_exists('property_exists')) {
	/**
	 * property_exists Computes the difference of arrays using keys for comparison 
   * (PHP 5 >= 5.1.0)
	 * 
	 * @param mixed  $context  Object or class name to inspect
	 * @param string $property Name of property
	 * 
	 * @return boolean
	 * @link http://php.net/property_exists
	 */
  function property_exists($context, $property) {
    $vars = is_object($context) ? 
      get_object_vars($context) : 
      get_class_vars($context);
    return array_key_exists($property, $vars);
  }
} 

if (!function_exists('memory_get_usage')) {
	/**
	 * Returns the amount of memory allocated to PHP, 
	 * (PHP 4 >= 4.3.2, PHP 5)
	 * requires compiling with --enable-memory-limit before 5.2.1
	 * 
	 * @param bool $real_usage Real memory if true, emalloc() if false
	 * 
	 * @return int Number of bytes
	 * @link http://php.net/memory_get_usage
	 */
  function memory_get_usage($real_usage = false) {
    return -1;
  }
}

if (!function_exists('memory_get_peak_usage')) {
  /**
   * Returns the peak of memory allocated by PHP
   * (PHP 5 >= 5.2.0)
   * requires compiling with --enable-memory-limit before 5.2.1
   * 
   * @param bool $real_usage Real memory if true, emalloc() if false
   * 
   * @return int Number of bytes
   * @link http://php.net/memory_get_peak_usage
   */
  function memory_get_peak_usage($real_usage = false) {
    return memory_get_usage($real_usage);
  }
}

if (!function_exists('timezone_identifiers_list')) {
	/**
	 * Returns numerically index array with all timezone identifiers
	 * (PHP 5 >= 5.1.0)
	 * 
	 * @param int    $what    One of DateTimeZone class constants
	 * @param string $country A two-letter ISO 3166-1 compatible country code. 
	 * 
	 * @return array The identifiers
	 */
  function timezone_identifiers_list($what = null, $country = null) {
    return include "timezones.php";
  }
}

if (!function_exists('mb_strtoupper')) {
  function mb_strtoupper($string) {
    return strtoupper($string);
  }
}

if (!function_exists('mb_strtolower')) {
  function mb_strtolower($string) {
    return strtolower($string);
  }
}

if (!function_exists('mb_convert_case')) {
  function mb_ucwords($string) {
    return ucwords($string);
  }
}
else {
  function mb_ucwords($string) {
    return mb_convert_case($string, MB_CASE_TITLE, CApp::$encoding);
  }
}

if (!defined('PHP_INT_MAX')) {
  define('PHP_INT_MAX', pow(2, 31)-1);
}

/**
 * (PHP 4, PHP 5)
 * bcmod Get modulus of an arbitrary precision number
 * 
 * cf. http://php.net/bcmod
 */
if (!function_exists('bcmod')) {
  function bcmod($left_operand, $modulus) {
    // how many numbers to take at once? carefull not to exceed (int)
    $take = 5;    
    $mod = '';
    do {
      $a = (int) $mod.substr($left_operand, 0, $take);
      $left_operand = substr($left_operand, $take);
      $mod = $a % $modulus;   
    } while (strlen($left_operand));
    return (int) $mod;
  }
}

/**
 * (PHP 5 >= 5.1.0)
 * date_default_timezone_set Sets the default timezone used by all date/time functions in a script 
 * @param object $timezone_identifier
 * @return 
 */
if (!function_exists("date_default_timezone_set")) {
  function date_default_timezone_set($timezone_identifier) {
    // void
  }
}

/**
 * (PHP 5 >= 5.1.0)
 * inet_pton Converts a human readable IP address to its packed in_addr representation
 */
if (!function_exists("inet_pton")) {
  function inet_pton($address) {
    // void
  }
}

/**
 * (PHP 5 >= 5.1.0)
 * inet_pton Converts a packed internet address to a human readable representation
 */
if (!function_exists("inet_ntop")) {
  function inet_ntop($in_addr) {
    return "";
  }
}