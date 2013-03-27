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
 * Generic range calculation.
 * A range is a given pair with a lower and upper bound
 * A null bound is infinite (minus for lower, plus for upper)
 */
abstract class CMbRange {
  /**
   * Tell whether range is void (empty)
   * @param object $lower The lower bound
   * @param object $upper The upper bound
   * @return boolean 
   */
  static function void($lower, $upper) {
    return ($upper < $lower && $lower !== null && $upper !== null);
  }

  /**
   * Tell whether range is finite
   * @param object $lower The lower bound
   * @param object $upper The upper bound
   * @return boolean 
   */
  static function finite($lower, $upper) {
    return ($lower !== null && $upper !== null);
  }

  /**
   * Tell whether given value is in range (permissive)
   * @param object $value The value to check
   * @param object $lower The lower bound
   * @param object $upper The upper bound
   * @return boolean 
   */
  static function in($value, $lower, $upper) {
    return 
      ($value <= $upper || $upper === null) && 
      ($value >= $lower || $lower === null);
  }
  
  /**
   * Tell whether two ranges collide (permissive)
   * @param object $lower1
   * @param object $upper1
   * @param object $lower2
   * @param object $upper2
   * @param boolean permissive
   * @return boolean
   */
  static function collides($lower1, $upper1, $lower2, $upper2, $permissive = true) {
    return 
      $permissive ?
        ($lower1 < $upper2 || $lower1 === null || $upper2 === null) && 
      ($upper1 > $lower2 || $upper1 === null || $lower2 === null) :
        ($lower1 <= $upper2 || $lower1 === null || $upper2 === null) && 
        ($upper1 >= $lower2 || $upper1 === null || $lower2 === null);
  }

  /**
   * Get the intersection of two ranges (permissive)
   * Result intersection might be empty, that is with upper < lower bound
   * @param object $lower1
   * @param object $upper1
   * @param object $lower2
   * @param object $upper2
   * @return array($lower, $upper)
   */
  static function intersection($lower1, $upper1, $lower2, $upper2) {
    return array (
      ($lower1 !== null && $lower2 !== null ) ? max($lower1, $lower2) : null,
      ($upper1 !== null && $upper2 !== null ) ? min($upper1, $upper2) : null,
    );
  }
  
  /**
   * Tell whether range1 is inside range2 (permissive)
   * @param object $lower1
   * @param object $upper1
   * @param object $lower2
   * @param object $upper2
   * @return boolean
   */
  static function inside($lower1, $upper1, $lower2, $upper2) {
    list($lower, $upper) = self::intersection($lower1, $upper1, $lower2, $upper2);
    return $lower == $lower1 && $upper = $upper1;
  }
  
  /**
   * Crop a range with another, resulting in 0 to 2 range fragments
   * Limitation: cropper has to be finite
   * @param object $lower1 Cropped range
   * @param object $upper1 Cropped range
   * @param object $lower2 Cropper range
   * @param object $upper2 Cropper range
   * @return array Array of range fragments, false on infinite cropper
   */
  static function crop($lower1, $upper1, $lower2, $upper2) {
    if (!self::finite($lower2, $upper2)) {
      return false;
    }
    
    $fragments = array();

    // No collision: cropped intact
    if (!self::collides($lower1, $upper1, $lower2, $upper2)) {
      $fragments[] = array($lower1, $upper1);
      return $fragments;
    }


    // Right fragment
    if ($lower2 <= $upper1 || $upper1 === null) {
      if (!self::void($lower1, $lower2)) {
        $fragments[] = array($lower1, $lower2);
      }
    }

    // Left fragment
    if ($upper2 >= $lower1 || $lower1 === null) {
      if (!self::void($upper2, $upper1)) {
        $fragments[] = array($upper2, $upper1);
      }
    }
    
    return $fragments;
  }

  /**
   * Crop a range with many another, resulting in 0 to n range fragments
   * Limitation: cropper has to be finite
   * @param object $lower Cropped range
   * @param object $upper Cropped range
   * @param array Array of cropper ranges
   * @return array Array of range fragments, false on infinite cropper
   */
  static function multiCrop($lower, $upper, $croppers) {
    $fragments = array(array($lower, $upper));

    foreach ($croppers as $_cropper) {
      $new_fragments = array();
      foreach ($fragments as $key => $_fragment) {
        $new_fragments = array_merge($new_fragments, self::crop($_fragment[0],$_fragment[1], $_cropper[0], $_cropper[1]));
      }
      $fragments = $new_fragments;
    }

    return $fragments;
  }
  
  static function forceInside($lower, $upper, $value) {
    $value = max($value, $lower);
    $value = min($value, $upper);
    return $value;
  }
  
  /**
   * rearrange a list of object in an optimized list
   * 
   * @param array   $intervals   $intervals key => array(lower, upper);
   * @param boolean $permissive  [optional]
   * @param array   &$uncollided array of uncollided elements
   *
   * @return array $lines lignes avec les keys positionned
   * @TODO : find a better way for uncollided
   */
  static function rearrange($intervals, $permissive = true, &$uncollided = array()) {
    if (!count($intervals)) {
      return array();
    }
    $lines = array();
    $uncollided = $intervals;
    // multisort ruins the keys if numeric
    if (!is_numeric(reset(array_keys($intervals)))) {
      array_multisort($intervals, SORT_ASC, CMbArray::pluck($intervals, "lower")); //order by lower elements ASC
    }
    foreach ($intervals as $_interval_id => $_interval) {
      foreach ($lines as &$_line) {
        $line_occupied = false;
        foreach ($_line as $_positioned_id) {
          $positioned = $intervals[$_positioned_id];
          if (CMbRange::collides($_interval["lower"], $_interval["upper"], $positioned["lower"], $positioned["upper"], $permissive)) {
            $line_occupied = true;
            unset($uncollided[$_positioned_id]);
            //continue 2; // Next line
          }
        }
        if ($line_occupied) {
          continue;
        }
          
        $_line[] = $_interval_id;
        continue 2; // Next interval
      }
      $lines[count($lines)] = array($_interval_id);
    }
    return $lines;
  }
}