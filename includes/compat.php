<?php
/**
 * Compat functions emulations
 *
 * PHP version 5.3.x+
 *  
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

/**
 * Recursively applies a function to values of an array
 * 
 * @param string $function Callback to apply
 * @param array  $array    Array to apply callback on
 * 
 * @return array
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
 * @return bool true if needle is found in the array, false otherwise.
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

if (!function_exists('getrusage')) {
  /**
   * Gets the current resource usages
   *
   * @param bool|int $who If who is 1, getrusage will be called with RUSAGE_CHILDREN
   *
   * @return array Results
   * @link http://php.net/memory_get_peak_usage
   */
  function getrusage($who = 0) {
    return array(
      "ru_utime.tv_usec" => -1,
      "ru_utime.tv_sec"  => -1,
      "ru_stime.tv_usec" => -1,
      "ru_stime.tv_sec"  => -1,
    );
  }
}

if (!function_exists('mb_strtoupper')) {
  /**
   * Make a string uppercase
   * Multi-byte graceful fallback
   * 
   * @param string $string Input string
   * 
   * @return string the uppercased string
   * @link http://php.net/manual/en/function.strtoupper.php
   */
  function mb_strtoupper($string) {
    return strtoupper($string);
  }
}

if (!function_exists('mb_strtolower')) {
  /**
   * Make a string lowecase
   * Multi-byte graceful fallback
   * 
   * @param string $string Input string
   * 
   * @return string the lowercased string
   * @link http://php.net/manual/en/function.strtolower.php
   */
  function mb_strtolower($string) {
    return strtolower($string);
  }
}

if (!function_exists('mb_convert_case')) {
  /**
   * Make a string with uppercased words
   * Multi-byte graceful fallback
   * 
   * @param string $string Input string
   * 
   * @return string The word uppercased string
   * @link http://php.net/manual/en/function.ucwords.php
   */
  function mb_ucwords($string) {
    return ucwords($string);
  }
}
else {
  /**
   * Make a string with uppercased words
   * Multi-byte graceful fallback
   * 
   * @param string $string Input string
   * 
   * @return string the word uppercased string
   * @link http://php.net/manual/en/function.ucwords.php
   */
  function mb_ucwords($string) {
    return mb_convert_case($string, MB_CASE_TITLE, CApp::$encoding);
  }
}

if (!function_exists('bcmod')) {
  /**
   * (PHP 4, PHP 5)
   * Get modulus of an arbitrary precision number
   * 
   * @param string $left_operand Any precision integer value
   * @param int    $modulus      Integer modulus
   * 
   * @return int Rest of modulus
   * @link http://php.net/bcmod
   */
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

if (!function_exists('mime_content_type')) {
  /**
   * (PHP 5 > 5.2)
   * Dectect the mime type of a file
   *
   * @param string $f Name of the file
   *
   * @return string Mime type
   */
  function mime_content_type($f) {
    return trim(exec('file -bi '.escapeshellarg($f)));
  }
}
