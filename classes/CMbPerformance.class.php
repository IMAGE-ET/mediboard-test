<?php

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Performance profling, to place markers at differents times
 */
class CMbPerformance {
  static $steps = array();

  private static $previous;

  private static $startTime;
  private static $endTime;
  private static $dbTime;

  /**
   * Tells if the application is in profiling mode
   *
   * @return bool
   */
  static function isProfiling() {
    static $cache = null;

    if ($cache === null) {
      if (isset($_COOKIE["mediboard-profiling"])) {
        $cookie = stripslashes($_COOKIE["mediboard-profiling"]);
        $cache = json_decode($cookie) == 1;
      }
      else {
        $cache = false;
      }
    }

    return $cache;
  }

  /**
   * Start the timer
   *
   * @return void
   */
  static function start(){
    self::$startTime = microtime(true);
    self::$previous = self::$startTime;
  }

  /**
   * Place a marking
   *
   * @param string $label Marker label
   *
   * @return void
   */
  static function mark($label) {
    if (!self::isProfiling()) {
      return;
    }

    $time = microtime(true);

    $duration = $time - self::$previous;
    $duration = (float)number_format($duration*1000, 5, ".", "");

    self::$steps[] = array(
      "label" => $label,
      "time"  => self::$previous*1000,
      "dur"   => $duration,
      "mem"   => memory_get_usage(true),
    );

    self::$previous = $time;
  }

  /**
   * Output the profiling data
   *
   * @return string
   */
  static function out() {
    self::$endTime = microtime(true);

    $data = array(
      "start" => self::$startTime * 1000,
      "end"   => self::$endTime * 1000,
      "steps" => self::$steps,
      "db"    => self::$dbTime,
    );

    return json_encode($data);
  }

  /**
   * Save database time
   *
   * @param float $dbTime Total database time
   *
   * @return void
   */
  static function setDBTime($dbTime) {
    self::$dbTime = $dbTime;
  }

  /**
   * Write HTTP header containing profiling data
   *
   * @return void
   */
  static function writeHeader() {
    if (!self::isProfiling()) {
      return;
    }

    if (headers_sent()) {
      return;
    }

    global $m, $a, $dosql;

    $req = "$m|".(empty($dosql) ? $a : $dosql);

    header("X-Mb-Timing: ".self::out());
    header("X-Mb-Req: $req");
  }
}
