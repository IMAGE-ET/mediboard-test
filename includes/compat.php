<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * Expose PHP implementation of missing built-in function
 */

/**
 * (PHP 5 >= 5.1.0)
 * array_diff_key Computes the difference of arrays using keys for comparison 
 * 
 * cf. http://php.net/array_diff_key
 */
 
if (!function_exists('array_diff_key')) {
  function array_diff_key() {
    $argCount  = func_num_args();
    $argValues  = func_get_args();
    $valuesDiff = array();
    if ($argCount < 2) return false;
    foreach ($argValues as $argParam) {
      if (!is_array($argParam)) return false;
    }
    foreach ($argValues[0] as $valueKey => $valueData) {
      for ($i = 1; $i < $argCount; $i++) {
        if (isset($argValues[$i][$valueKey])) continue 2;
      }
      $valuesDiff[$valueKey] = $valueData;
    }
    return $valuesDiff;
  }
}


/**
 * (PHP 5 >= 5.1.0)
 * property_exists Computes the difference of arrays using keys for comparison 
 * 
 * cf. http://php.net/property_exists
 */
if (!function_exists('property_exists')) {
  function property_exists($class, $property) {
    $vars = is_object($class) ? 
              get_object_vars($class) : 
              get_class_vars($class);
    return array_key_exists($property, $vars);
  }
} 

/**
 * (PHP 4 >= 4.3.2, PHP 5)
 * memory_get_usage Returns the amount of memory allocated to PHP, 
 * requires compiling with --enable-memory-limit before 5.2.1
 * 
 * cf. http://php.net/memory_get_usage
 */
if(!function_exists('memory_get_usage')) {
  function memory_get_usage($real_usage = false) {
    /*$os = php_uname('s');
    $pid = getmypid();
    if (substr($os, 0, 3) === 'WIN') {
      exec("tasklist /FI \"PID eq $pid\" /FO LIST", $output);
      return preg_replace('/[\D]/', '', $output[5]) * 1024;
    }
    else {
      exec("ps -eo%mem,rss,pid | grep $pid", $output);
      $output = explode(' ', $output[0]);
      return $output[1] * 1024;
    } */
    return '-';
  }
}


/**
 * (PHP 5 >= 5.2.0, PECL json:1.2.0-1.2.1)
 * json_encode Returns the JSON representation of a value
 * 
 * cf. http://php.net/json_encode
 */
if(!function_exists('json_encode')) {
  function json_encode($object) {
    // create a new instance of Services_JSON
    $json = new Services_JSON();
    $sJson = html_entity_decode($json->encode($object), ENT_NOQUOTES);
    
    return str_replace("&quot;", "\\\"", $sJson);
  }
}

/**
 * (PHP 5 >= 5.1.0)
 * timezone_identifiers_list - Returns numerically index array with all timezone identifiers
 * @param int $what
 * @param string $country
 * @return array The identifiers
 */
if(!function_exists('timezone_identifiers_list')) {
  function timezone_identifiers_list($what = null, $country = null) {
    return include('timezones.php');
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

if (!defined('PHP_INT_MAX')) {
	define('PHP_INT_MAX', pow(2, 31)-1);
}

?>