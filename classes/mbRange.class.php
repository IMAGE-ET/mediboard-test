<?php /* $Id: mbArray.class.php 10776 2010-12-02 14:22:08Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 10776 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
    return ($upper < $lower || $lower === null || $upper === null);
  }

	/**
	 * Tell whether given value is in range (permissive)
	 * @param object $value The value to check
	 * @param object $lower The lower bound
	 * @param object $upper The upper bound
	 * @return boolean 
	 */
	static function in($value, $lower, $upper) {
	  return ($value <= $upper || $upper === null) && ($value >= $lower || $lower === null);
	}
	
	/**
	 * Tell whether two ranges collide (permissive)
	 * @param object $lower1
	 * @param object $upper1
	 * @param object $lower2
	 * @param object $upper2
	 * @return boolean
	 */
	static function collides($lower1, $upper1, $lower2, $upper2) {
		return ($lower1 <= $upper2 || $upper2 === null) && ($upper1 >= $lower2 || $lower1 === null);
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
}
?>