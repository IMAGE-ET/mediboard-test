<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Utility methods for arrays
 */
abstract class CMbArray {

  /**
   * Compares the content of two arrays
   *
   * @param array $array1 The first array
   * @param array $array2 The second array
   *
   * @return array An associative array with values
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
   *
   * @param array $array1 The first array
   * @param array $array2 The second array
   *
   * @return array The difference
   */
  static function diffRecursive($array1, $array2) {
    foreach ($array1 as $key => $value) {
      // Array value
      if (is_array($value)) {
        if (!isset($array2[$key])) {
          $difference[$key] = $value;
        }
        elseif (!is_array($array2[$key])) {
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
   *
   * @param mixed $needle    Value to remove
   * @param array &$haystack Array to alter
   * @param bool  $strict    Strict search
   *
   * @return int Occurences count
   */
  static function removeValue($needle, &$haystack, $strict = false) {
    $count = 0;
    while (($key = array_search($needle,  $haystack, $strict)) !== false) {
      unset($haystack[$key]);
      $count++;
    }
    return $count;
  }

  /**
   * Get the previous and next key
   *
   * @param array  $arr The array to seek in
   * @param string $key The target key
   *
   * @return array Previous and next key in an array, null if unavailable
   */
  static function getPrevNextKeys($arr, $key){
    $keys = array_keys($arr);
    $keyIndexes = array_flip($keys);

    $return = array();
    if (isset($keys[$keyIndexes[$key]-1])) {
      $return["prev"] = $keys[$keyIndexes[$key]-1];
    }
    else {
      $return["prev"] = null;
    }

    if (isset($keys[$keyIndexes[$key]+1])) {
      $return["next"] = $keys[$keyIndexes[$key]+1];
    }
    else {
      $return["next"] = null;
    }

    return $return;
  }

  /**
   * Merge recursively two array
   *
   * @param array $paArray1 First array
   * @param array $paArray2 The array to be merged
   *
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
   *
   * @param array ... Any number of arrays to merge
   *
   * @return array The merge result
   */
  static function mergeKeys(){
    $args = func_get_args();
    $result = array();
    foreach ($args as $array) {
      foreach ($array as $key => $value) {
        $result[$key] = $value;
      }
    }
    return $result;
  }


  /**
   * Returns the value following the given one in cycle mode
   *
   * @param array $array The array of values to cycle on
   * @param mixed $value The reference value
   *
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
   *
   * @param array  $array   The array to explore
   * @param string $key     Name of the key to extract
   * @param mixed  $default The default value if $key is not found
   *
   * @return mixed The value corresponding to $key in $array if it exists, else $default
   */
  static function get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
  }

  /**
   * Returns the first value of the array that isset, from keys
   *
   * @param array $array   The array to explore
   * @param array $keys    The keys to read
   * @param mixed $default The default value no value is found
   *
   * @return mixed The first value found
   */
  static function first($array, $keys, $default = null) {
    foreach ($keys as $key) {
      if (isset($array[$key])) {
        return $array[$key];
      }
    }
    return $default;
  }

  /**
   * Extract a key from an array, returning the value if exists
   *
   * @param array  &$array    The array to explore
   * @param string $key       Name of the key to extract
   * @param mixed  $default   The default value is $key is not found
   * @param bool   $mandatory Will trigger an warning if value is null
   *
   * @return mixed The extracted value
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
   *
   * @param array &$array The array to alter
   * @param mixed $key    The key to check
   * @param mixed $value  The default value if key is not set
   *
   * @return void
   */
  static function defaultValue(&$array, $key, $value) {
    // Should not use isset
    if (!array_key_exists($key, $array)) {
      $array[$key] = $value;
    }
  }

  /**
   * Return a string of XML attributes based on given array key-value pairs
   *
   * @param array $array The source array
   *
   * @return string String attributes like 'key1="value1" ... keyN="valueN"'
   */
  static function makeXmlAttributes($array) {
    $return = '';
    foreach ($array as $key => $value) {
      if ($value !== null) {
        $value = trim(CMbString::htmlSpecialChars($value));
        $return .= "$key=\"$value\" ";
      }
    }
    return $return;
  }

  /**
   * Pluck (collect) given key or attribute name of each value
   * whether the values are arrays or objects. Preserves indexes
   *
   * @param mixed $array The array or object to pluck
   * @param mixed $name  The key or attribute name
   *
   * @return array All plucked values
   */
  static function pluck($array, $name) {
    if (!is_array($array)) {
      return null;
    }

    // Recursive multi-dimensional call
    $args = func_get_args();
    if (count($args) > 2) {
      $name = array_pop($args);
      $array = call_user_func_array(array("CMbArray", "pluck"), $args);
    }

    $values = array();
    foreach ($array as $key => $item) {
      if (is_object($item)) {
        if (!property_exists($item, $name)) {
          trigger_error("Object at key '$key' doesn't have the '$name' property", E_USER_WARNING);
          continue;
        }

        $values[$key] = $item->$name;
        continue;
      }

      if (is_array($item)) {
        if (!array_key_exists($name, $item)) {
          trigger_error("Array at key '$key' doesn't have a value for '$name' key", E_USER_WARNING);
          continue;
        }

        $values[$key] = $item[$name];
        continue;
      }

      trigger_error("Item at key '$key' is neither an array nor an object", E_USER_WARNING);
    }

    return $values;
  }

  /**
   * Create an array with filtered keys based on having given prefix
   *
   * @param array  $array  The array to filter
   * @param string $prefix The prefix that has to start key strings
   *
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

  /**
   * Transpose a 2D matrix
   *
   * @param array $array The matrix to transpose
   *
   * @return array The transposed matrix
   */
  static function transpose($array) {
    $out = array();
    foreach ($array as $key => $subarr) {
      foreach ($subarr as $subkey => $subvalue) {
        $out[$subkey][$key] = $subvalue;
      }
    }
    return $out;
  }

  /**
   * Call a method on each object of the array
   *
   * @param object $array  The array of objects
   * @param string $method The method to call on each array
   *
   * @return array The array of objects after the method is called
   */
  static function invoke($array, $method) {
    $args = func_get_args();
    $args = array_slice($args, 2);

    foreach ($array as $object) {
      call_user_func_array(array($object, $method), $args);
    }

    return $array;
  }

  /**
   * Insert a key-value pair after a specific key
   *
   * @param array  &$array  The source array
   * @param string $ref_key The reference key
   * @param string $key     The new key
   * @param mixed  $value   The new value to insert after $_ref_key
   *
   * @return void
   */
  static function insertAfterKey(&$array, $ref_key, $key, $value) {
    $keys = array_keys($array);
    $vals = array_values($array);

    $insertAfter = array_search($ref_key, $keys)+1;

    $keys2 = array_splice($keys, $insertAfter);
    $vals2 = array_splice($vals, $insertAfter);

    $keys[] = $key;
    $vals[] = $value;

    $array = array_merge(array_combine($keys, $vals), empty($keys2) ? array() : array_combine($keys2, $vals2));
  }

  /**
   * Return the standard average of an array
   *
   * @param array $array Scalar values
   *
   * @return float Average value
   */
  static function average($array) {
    if (!is_array($array)) {
      return null;
    }

    return array_sum($array) / count($array);
  }

  /**
   * Return the standard variance of an array
   *
   * @param array $array Scalar values
   *
   * @return float: ecart-type
   */
  static function variance($array) {
    if (!is_array($array)) {
      return null;
    }

    $moyenne = self::average($array);
    $sigma = 0;
    foreach ($array as $value) {
      $sigma += pow((floatval($value)-$moyenne), 2);
    }

    return sqrt($sigma / count($array));
  }

  /**
   * Check whether a value is in array
   *
   * @param mixed $needle   The searched value
   * @param mixed $haystack Array or token space separated string
   * @param bool  $strict   Type based comparaison
   *
   * @return bool
   */
  static function in($needle, $haystack, $strict = false) {
    if (is_string($haystack)) {
      $haystack = explode(" ", $haystack);
    }

    return in_array($needle, $haystack, $strict);
  }

  /**
   * Exchanges all keys with their associated values in an array,
   * and keep all the values if there are several occurrences
   *
   * @param array $trans The array to flip
   *
   * @return array[]
   */
  static function flip($trans) {
    $result = array();
    foreach ($trans as $_key => $_value) {
      if (!array_key_exists($_value, $result)) {
        $result[$_value] = array($_key);
      }
      else {
        $result[$_value][] = $_key;
      }
    }

    return $result;
  }

  static function countLeafs($array) {
    if (!is_array($array)) {
      return 1;
    }

    $count = 0;
    foreach ($array as $_value) {
      $count += self::countLeafs($_value);
    }
    return $count;
  }

  /**
   * Sort an array by using another array.
   * The values of the $order must be the keys of the $array, in the right order.
   *
   * @param array $array The array to sort
   * @param array $order The
   *
   * @return array The sorted array
   */
  static function ksortByArray($array, $order) {
    $ordered = array();
    foreach ($order as $key) {
      if (array_key_exists($key, $array)) {
        $ordered[$key] = $array[$key];
        unset($array[$key]);
      }
    }
    return $ordered;
  }

  /**
   * Sort an array of objects by property name given in parameter
   *
   * @param array  &$objects The objects array to sort
   * @param string $prop     The property name
   * @param string $propAlt  The alternative property name
   *
   * @return bool Sucess or Failure
   */
  static function ksortByProp(&$objects, $prop, $propAlt = null) {
    usort($objects, CMbArray::objectSorter($prop, $propAlt));
  }

  /**
   * Get a comparaison fonction for two objects
   * using a property (used by ksortByProp)
   *
   * @param string $prop    The property name
   * @param string $propAlt The alternative property name
   *
   * @return callable The fonction
   */
  static function objectSorter($prop, $propAlt = null) {
    return function ($object1, $object2) use ($prop, $propAlt) {
      $compare1 = $object1->$prop;
      $compare2 = $object2->$prop;
      if ($propAlt && ($compare1 == $compare2)) {
        return strnatcmp($object1->$propAlt, $object2->$propAlt);
      }
      return strnatcmp($compare1, $compare2);

    };
  }

  /**
   * A recursive version of array_search (works for multidimensional array).
   * The result is an array reproducing the structure of the haystack
   *
   * @param mixed $needle   The needle
   * @param array $haystack The haystack
   *
   * @return array
   */
  static function searchRecursive($needle, $haystack) {
    $path = array();
    foreach ($haystack as $id => $val) {
      if ($val === $needle) {
        $path[] = $id;

        break;
      }
      elseif (is_array($val)) {
        $found = CMbArray::searchRecursive($needle, $val);
        if (count($found)>0) {
          $path[$id] = $found;

          break;
        }
      }
    }

    return $path;
  }

  /**
   * Get a value in a $tree, from a $path built with $separator
   *
   * @param array  $tree      The tree containing the value
   * @param string $path      The path to browse
   * @param string $separator The separator used in the path
   *
   * @return mixed
   */
  static function readFromPath($tree, $path, $separator = " ") {
    if (!$path) {
      return $tree;
    }

    $items = explode($separator, $path);
    foreach ($items as $part) {
      $tree = $tree[$part];
    }

    return $tree;
  }

  /**
   * Count the occurrences of the given value
   *
   * @param mixed $needle The searched value
   * @param array $haystack The array
   * @param bool  $strict If true, strict comparison (===) will be used
   *
   * @return int
   */
  static function countValues($needle, $haystack, $strict = false) {
    return count(array_keys($haystack, $needle, $strict));
  }
}
