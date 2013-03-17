<?php

/**
 * $Id$
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Date utility class
 */
class CMbDate {
  static $secs_per = array (
    "year"   => 31536000, // 60 * 60 * 24 * 365
    "month"  =>  2592000, // 60 * 60 * 24 * 30
    "week"   =>   604800, // 60 * 60 * 24 * 7
    "day"    =>    86400, // 60 * 60 * 24
    "hour"   =>     3600, // 60 * 60
    "minute" =>       60, // 60
    "second" =>        1, // 1
  );

  static $xmlDate     = "%Y-%m-%d";
  static $xmlTime     = "%H:%M:%S";
  static $xmlDateTime = "%Y-%m-%dT%H:%M:%S";

  /**
   * Compute real relative achieved gregorian durations in years and months
   *
   * @param date $from Starting time
   * @param date $to   Ending time, now if null
   *
   * @return array[int] Number of years and months
   */
  static function achievedDurations($from, $to = null) {
    $achieved = array(
      "year"  => "??",
      "month" => "??",
    );

    if ($from == "0000-00-00" || !$from) {
      return $achieved;
    }

    if (!$to) {
      $to = CMbDT::date();
    }

    list($yf, $mf, $df) = explode("-", $from);
    list($yt, $mt, $dt) = explode("-", $to);

    $achieved["month"] = 12*($yt-$yf) + ($mt-$mf);
    if ($mt == $mf && $dt < $df) {
      $achieved["month"]--;
    }

    $achieved["year"] = intval($achieved["month"] / 12);
    return $achieved;
  }

  /**
   * Compute user friendly approximative duration between two date time
   *
   * @param datetime $from      From time
   * @param datetime $to        To time, now if null
   * @param int      $min_count The minimum count to reach the upper unit, 2 if undefined
   *
   * @return array("unit" => string, "count" => int)
   */
  static function relative($from, $to = null, $min_count = 2) {
    if (!$from) {
      return null;
    }

    if (!$to) {
      $to = CMbDT::dateTime();
    }

    // Compute diff in seconds
    $diff = strtotime($to) - strtotime($from);

    // Find the best unit
    foreach (self::$secs_per as $unit => $secs) {
      if (abs($diff / $secs) > $min_count) {
        break;
      }
    }

    return array (
      "unit"  => $unit,
      "count" => intval($diff / $secs),
    );
  }

  /**
   * Get the month number for a given datetime
   *
   * @param datetime $date Datetime
   *
   * @return int The month number
   */
  static function monthNumber($date) {
    return intval(CMbDT::transform(null, $date, "%m"));
  }

  /**
   * Get the week number for a given datetime
   *
   * @param datetime $date Datetime
   *
   * @return int The week number
   */
  static function weekNumber($date) {
    return intval(date("W", strtotime($date)));
  }

  /**
   * Get the week number in the month
   *
   * @param datetime $date Date
   *
   * @return int The week number
   */
  static function weekNumberInMonth($date) {
    $month = self::monthNumber($date);
    $week_number = 0;

    do {
      $date = CMbDT::date("-1 WEEK", $date);
      $_month = self::monthNumber($date);
      $week_number++;
    } while ($_month == $month);

    return $week_number;
  }

  /**
   * Give a Dirac hash of given datetime
   *
   * @param string   $period   One of minute, hour, day, week, month or year
   * @param datetime $datetime Datetime
   *
   * @return datetime Hash
   */
  static function dirac($period, $datetime) {
    switch ($period) {
      case "min":
        return CMbDT::transform(null, $datetime, "%Y-%m-%d %H:%M:00");
      case "hour":
        return CMbDT::transform(null, $datetime, "%Y-%m-%d %H:00:00");
      case "day":
        return CMbDT::transform(null, $datetime, "%Y-%m-%d 00:00:00");
      case "week":
        return CMbDT::transform("last sunday +1 day", $datetime, "%Y-%m-%d 00:00:00");
      case "month":
        return CMbDT::transform(null, $datetime, "%Y-%m-01 00:00:00");
      case "year":
        return CMbDT::transform(null, $datetime, "%Y-01-01 00:00:00");
      default:
        trigger_error("Can't make a Dirac hash for unknown '$period' period", E_USER_WARNING);
    }
  }

  /**
   * Give a position to a datetime relative to a reference
   *
   * @param dateTime $datetime  Datetime
   * @param dateTime $reference Reference
   * @param string   $period    One of 1hour, 6hours, 1day
   *
   * @return float
   */
  static function position($datetime, $reference, $period) {
    $diff = strtotime($datetime) - strtotime($reference);

    switch ($period) {
      case "1hour":
        return $diff / CMbDate::$secs_per["hour"];
      case "6hours":
        return $diff / (CMbDate::$secs_per["hour"] * 6);
      case "1day":
        return $diff / CMbDate::$secs_per["day"];
      default:
        trigger_error("Can't proceed for unknown '$period' period", E_USER_WARNING);
    }
  }

  /**
   * Turn a datetime to its UTC timestamp equivalent
   *
   * @param dateTime $datetime Datetime
   *
   * @return int
   */
  static function toUTCTimestamp($datetime) {
    static $default_timezone;

    if (!$default_timezone) {
      $default_timezone = date_default_timezone_get();
    }

    // Temporary change timezone to UTC
    date_default_timezone_set("UTC");
    $datetime = strtotime($datetime) * 1000; // in ms;
    date_default_timezone_set($default_timezone);

    return $datetime;
  }
}
