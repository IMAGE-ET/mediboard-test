<?php

/**
 * $Id$
 *  
 * @category mediboard
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CModelObjectFieldDescription {
  /**
   * Patient Import for cegi
   *
   * @param $object CPatient|CSejour
   *
   * @return array $object_specs
   */
  static function getSpecList($object) {
    $object_specs["main"] = array_values($object->_specs);
    self::cleanSpecs($object_specs["main"]);
    return $object_specs;
  }

  /**
   * cleanup the array
   *
   * @param array &$array_spec array to clean
   *
   * @return null
   */
  function cleanSpecs(&$array_spec) {
    foreach ($array_spec as $key => $_spec) {
      if ($_spec instanceof CRefSpec) {
        unset($array_spec[$key]);
      }

      if (substr($_spec->fieldName, 0, 1) == "_") {
        unset($array_spec[$key]);
      }
    }
  }

  /**
   * remove specs from the list
   *
   * @param array          $specs_to_remove array of fieldname to remove
   * @param CMbFieldSpec[] &$specs_array    array of specs
   *
   * @return mixed
   */
  static function removeSpecs($specs_to_remove, &$specs_array) {
    if (!count($specs_to_remove)) {
      return $specs_array;
    }
    foreach ($specs_array["main"] as $key => $_spec) {
      if (in_array($_spec->fieldName, $specs_to_remove)) {
        unset($specs_array["main"][$key]);
      }
    }
    return $specs_array;
  }

  /**
   * Add a spec before another one
   *
   * @param CmbFieldSpec   &$spec        spec to add
   * @param CmbFieldSpec[] &$specs_array spec array
   * @param string         $key          key of the spec
   * @param bool           $notNull      notNull ?
   *
   * @return null
   */
  static function addBefore(&$spec, &$specs_array, $key = "main", $notNull = false) {
    if (!isset($specs_array[$key])) {
      $specs_array = array($key => array()) +$specs_array;
    }
    $spec->notNull = $notNull ? 1 : 0;
    array_unshift($specs_array[$key], $spec);
  }

  /**
   * add a spec after another one
   *
   * @param CMbFieldSpec   &$spec        spec to add
   * @param CMbFieldSpec[] &$specs_array spec array
   * @param string         $key          key
   * @param bool           $notNull      is the spec not null
   *
   * @return null
   */
  static function addAfter(&$spec, &$specs_array, $key = "main", $notNull = false) {
    $spec->notNull = ($notNull != $spec->notNull) ? $notNull: $spec->notNull ;
    $specs_array[$key][] = $spec;
  }

  /**
   * get the array of spec
   *
   * @param array $array the array to return
   *
   * @return array
   */
  static function getArray($array) {
    $return = array();
    foreach ($array as $key => $value) {
      foreach ($value as $_value) {
        $return[] = $_value;
      }
    }

    return $return;
  }
}
