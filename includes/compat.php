<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage Style
 * @version $Revision: 1913 $
 * @author Thomas Despoix
 * 
 * Expose PHP implementation of missing built-in function
 */


/**
 * (PHP 5 >= 5.1.0)
 * array_diff_key — Computes the difference of arrays using keys for comparison 
 * 
 * cf. http://php.net/manual/en/function.array-diff-key.php
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


?>