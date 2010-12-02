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
 * A range is a given pair with a min and max bound
 * A null bound is infinite
 */
abstract class CMbRange {
	/**
	 * Check if given value is in given range or equals to limit
	 * @param $value mixed The value to check
	 * @param $min mixed The min bound
	 * @param $max mixed The max bound
	 * @return bool 
	 */
	static function in($value, $min, $max) {
	  return ($value <= $max || $max === null) && ($value >= $min || $min === null);
	}
}
?>