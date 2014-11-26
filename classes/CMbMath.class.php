<?php

/**
 * $Id$
 *  
 * @category 
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Math utilities
 */
class CMbMath {
  /**
   * Round numbers using significant digits
   *
   * @param float $number The number to round
   * @param int   $n      The significant digits to keep
   *
   * @return float
   */
  static function roundSig($number, $n = 4) {
    if ($number == 0) {
      return 0;
    }

    $d = ceil(log10(abs($number)));
    $power = $n - $d;

    return round($number, $power);
  }
}
