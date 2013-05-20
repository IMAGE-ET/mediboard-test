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
 * Date and time manipulation class
 */
class CMbDT {
  // ISO date formats
  const ISO_DATE     = "%Y-%m-%d";
  const ISO_TIME     = "%H:%M:%S";
  const ISO_DATETIME = "%Y-%m-%d %H:%M:%S";

  // XML date formats
  const XML_DATE     = "%Y-%m-%d";
  const XML_TIME     = "%H:%M:%S";
  const XML_DATETIME = "%Y-%m-%dT%H:%M:%S";

  /**
   * Transforms absolute or relative time into a given format
   *
   * @param string $relative A relative time
   * @param string $ref      An absolute time to transform
   * @param string $format   The format in which the date will be returned
   *
   * @return string The transformed date
   */
  static function transform($relative, $ref, $format) {
    static $system_datetime = null;

    if ($system_datetime === null) {
      $system_datetime = CAppUI::conf("system_date");
    }

    if ($relative === "last sunday") {
      $relative .= " 12:00:00";
    }

    $timestamp = $ref ? strtotime($ref) : ($system_datetime ? strtotime($system_datetime." ".date("H:i:s")) : time());
    if ($relative) {
      $timestamp = strtotime($relative, $timestamp);
    }

    return strftime($format, $timestamp);
  }

  /**
   * Transforms absolute or relative date into DB friendly DATETIME format
   *
   * @param string $relative Modifies the date (eg '+1 DAY')
   * @param string $ref      The reference date time fo transform
   *
   * @return string The date
   **/
  static function date($relative = null, $ref = null) {
    return self::transform($relative, $ref, self::ISO_DATE);
  }

  /**
   * Transforms absolute or relative time into DB friendly DATETIME format
   *
   * @param string $relative Modifies the time (eg '+1 DAY')
   * @param string $ref      The reference time time fo transform
   *
   * @return string The time
   **/
  static function time($relative = null, $ref = null) {
    return self::transform($relative, $ref, self::ISO_TIME);
  }

  /**
   * Transforms absolute or relative datetime into DB friendly DATETIME format
   *
   * @param string $relative Modifies the datetime (eg '+1 DAY')
   * @param string $ref      The reference datetime fo transform
   *
   * @return string The datetime
   **/
  static function dateTime($relative = null, $ref = null) {
    return self::transform($relative, $ref, self::ISO_DATETIME);
  }

  /**
   * Transforms absolute or relative time into XML DATETIME format
   *
   * @param string $relative Modifies the time (eg '+1 DAY')
   * @param string $ref      The reference date time fo transforms
   *
   * @return string The transformed time
   **/
  static function dateTimeXML($relative = null, $ref = null) {
    return self::transform($relative, $ref, self::XML_DATETIME);
  }

  /**
   * Converts an xs;duration XML duration into a DB friendly DATETIME
   *
   * @param string $duration Duration with format P1Y2M3DT10H30M0S
   *
   * @return string The DATETIME, null if failed
   **/
  static function dateTimeFromXMLDuration($duration) {
    $regexp = "/P((\d+)Y)?((\d+)M)?((\d+)D)?T((\d+)H)?((\d+)M)?((\d+)S)?/";
    if (!preg_match($regexp, $duration, $matches)) {
      return null;
    }

    return sprintf(
      "%d-%d-%d %d:%d:%d",
      $matches[ 2], $matches[ 4], $matches[ 6],
      $matches[ 8], $matches[10], $matches[12]
    );
  }

  /**
   * Add a relative time to a reference time
   *
   * @param string $relative The relative time to add
   * @param string $ref      The reference time
   *
   * @return string The resulting time
   **/
  static function addTime($relative = null, $ref = null) {
    $fragments = explode(":", $relative);
    $hours   = CValue::read($fragments, 0, 0);
    $minutes = CValue::read($fragments, 1, 0);
    $seconds = CValue::read($fragments, 2, 0);

    return self::time("+$hours HOURS $minutes MINUTES $seconds SECONDS", $ref);
  }

  /**
   * Add a relative time to a reference datetime
   *
   * @param string $relative The relative time to add
   * @param string $ref      The reference datetime
   *
   * @return string The resulting time
   **/
  static function addDateTime($relative, $ref = null) {
    $fragments = explode(":", $relative);
    $hours   = CValue::read($fragments, 0, 0);
    $minutes = CValue::read($fragments, 1, 0);
    $seconds = CValue::read($fragments, 2, 0);

    return self::dateTime("+$hours HOURS $minutes MINUTES $seconds SECONDS", $ref);
  }

  /**
   * Substract a relative time to a reference time
   *
   * @param string $relative The relative time to substract
   * @param string $ref      The reference time
   *
   * @return string The resulting time
   **/
  static function subTime($relative = null, $ref = null) {
    $fragments = explode(":", $relative);
    $hours   = CValue::read($fragments, 0, 0);
    $minutes = CValue::read($fragments, 1, 0);
    $seconds = CValue::read($fragments, 2, 0);

    return self::time("-$hours HOURS -$minutes MINUTES -$seconds SECONDS", $ref);
  }

  /**
   * Count days between two datetimes
   *
   * @param string $from From datetime
   * @param string $to   To datetime
   *
   * @return int Days count
   **/
  static function daysRelative($from, $to) {
    if (!$from || !$to) {
      return null;
    }

    $from = intval(strtotime($from) / 86400);
    $to   = intval(strtotime($to  ) / 86400);

    return intval($to - $from);
  }

  /**
   * Count hours between two datetimes
   *
   * @param string $from From datetime
   * @param string $to   To datetime
   *
   * @return int Days count
   **/
  static function hoursRelative($from, $to) {
    if (!$from || !$to) {
      return null;
    }

    $from = intval(strtotime($from) / 3600);
    $to   = intval(strtotime($to  ) / 3600);

    return intval($to - $from);
  }

  /**
   * Count minutes between two datetimes
   *
   * @param string $from From datetime
   * @param string $to   To datetime
   *
   * @return int Days count
   **/
  static function minutesRelative($from, $to) {
    if (!$from || !$to) {
      return null;
    }

    $from = intval(strtotime($from) / 60);
    $to   = intval(strtotime($to  ) / 60);

    return intval($to - $from);
  }

  /**
   * Compute time duration between two datetimes
   *
   * @param string $from   From date
   * @param string $to     To date
   * @param string $format Format for time (sprintf syntax)
   *
   * @return string hh:mm:ss diff duration
   **/
  static function timeRelative($from, $to, $format = "%02d:%02d:%02d") {
    $diff = strtotime($to) - strtotime($from);
    $hours = intval($diff / 3600);
    $mins  = intval(($diff % 3600) / 60);
    $secs  = intval($diff % 60);

    return sprintf($format, $hours, $mins, $secs);
  }

  /**
   * Counts the number of intervals between reference and relative
   *
   * @param string $from     From time
   * @param string $to       To time
   * @param string $interval Interval time
   *
   * @return int Number of intervals
   **/
  static function timeCountIntervals($from, $to, $interval) {
    $zero     = strtotime("00:00:00");
    $from     = strtotime($from    ) - $zero;
    $to       = strtotime($to      ) - $zero;
    $interval = strtotime($interval) - $zero;

    return intval(($to - $from) / $interval);
  }

  /**
   * Retrieve nearest time (Dirac-like) with intervals
   *
   * @param string $reference     Reference time
   * @param string $mins_interval Minutes count
   *
   * @return string Nearest time
   **/
  static function timeGetNearestMinsWithInterval($reference, $mins_interval) {
    $min_reference = self::transform(null, $reference, "%M");
    $div = intval($min_reference / $mins_interval);
    $borne_inf = $mins_interval * $div;
    $borne_sup = $mins_interval * ($div + 1);
    $mins_replace = ($min_reference - $borne_inf) < ($borne_sup - $min_reference) ?
      $borne_inf :
      $borne_sup;

    $reference = ($mins_replace == 60) ?
      sprintf('%02d:00:00',   self::transform(null, $reference, "%H")+1) :
      sprintf('%02d:%02d:00', self::transform(null, $reference, "%H"), $mins_replace);

    return $reference;
  }

  /**
   * Calculate the bank holidays in France
   *
   * @param string $date The relative date, used to calculate the bank holidays of a specific year
   *
   * @deprecated Use CMbDate::getHolidays($date) instead
   * @return array List of bank holidays as dates
   **/
  static function bankHolidays($date = null) {
    return CMbDate::getHolidays($date);
  }

  /**
   * Return the Easter Date following a date
   *
   * @param string $date Reference date
   *
   * @return string the Easter date (Y-m-d)
   */

  static function getEasterDate($date = null) {
    if (!$date) {
      $date = CMbDT::date();
    }
    $year = CMbDT::transform("+0 DAY", $date, "%Y");
    $n = $year - 1900;
    $a = $n % 19;
    $b = intval((7 * $a + 1) / 19);
    $c = ((11 * $a) - $b + 4) % 29;
    $d = intval($n / 4);
    $e = ($n - $c + $d + 31) % 7;
    $P = 25 - $c - $e;
    if ($P > 0) {
      $P = "+".$P;
    }
    return CMbDT::date("$P DAYS", "$year-03-31");
  }

  /**
   * Calculate the number of work days in the given month date
   *
   * @param string $date The relative date of the months to get work days
   *
   * @return integer Number of work days
   **/
  static function workDaysInMonth($date = null) {
    $result = 0;
    if (!$date) {
      $date = self::date();
    }

    $debut  = $date;
    $rectif = self::transform("+0 DAY", $debut, "%d")-1;
    $debut  = self::date("-$rectif DAYS", $debut);
    $fin    = $date;
    $rectif = self::transform("+0 DAY", $fin, "%d")-1;
    $fin    = self::date("-$rectif DAYS", $fin);
    $fin    = self::date("+ 1 MONTH", $fin);
    $fin    = self::date("-1 DAY", $fin);

    $freeDays = self::bankHolidays($date);

    for ($i = $debut; $i <= $fin; $i = self::date("+1 DAY", $i)) {
      $day = self::transform("+0 DAY", $i, "%u");
      if ($day == 6 && !in_array($i, $freeDays)) {
        $result += 0.5;
      }
      elseif ($day != 7 and !in_array($i, $freeDays)) {
        $result += 1;
      }
    }

    return $result;
  }

  /**
   * Tell whether date is lunar
   *
   * @param string $date Date to check
   *
   * @return boolean
   **/
  static function isLunarDate($date) {
    $fragments = explode("-", $date);

    return ($fragments[2] > 31) || ($fragments[1] > 12);
  }

  /**
   * Convert a date from ISO to locale format
   *
   * @param string $date Date in ISO format
   *
   * @return string Date in locale format
   */
  static function dateToLocale($date) {
    return preg_replace("/(\d{4})-(\d{2})-(\d{2})/", '$3/$2/$1', $date);
  }

  /**
   * Convert a date from locale to ISO format
   *
   * @param string $date Date in locale format
   *
   * @return string Date in ISO format
   */
  static function dateFromLocale($date) {
    return preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", '$3-$2-$1', $date);
  }

  /**
   * Convert a datetime from LDAP to ISO format
   *
   * @param string $dateLargeInt nano seconds (yes, nano seconds) since jan 1st 1601
   *
   * @return string DateTime in ISO format
   */
  static function dateTimeFromLDAP($dateLargeInt) {
    // seconds since jan 1st 1601
    $secsAfterADEpoch = $dateLargeInt / (10000000);
    // unix epoch - AD epoch * number of tropical days * seconds in a day
    $ADToUnixConvertor = ((1970-1601) * 365.242190) * 86400;
    // unix Timestamp version of AD timestamp
    $unixTsLastLogon = intval($secsAfterADEpoch-$ADToUnixConvertor);

    return date("d-m-Y H:i:s", $unixTsLastLogon);
  }

  /**
   * Convert a datetime from ActiveDirecetory to ISO format
   *
   * @param string $dateAD Datetime from AD since jan 1st 1601
   *
   * @return string DateTime in ISO format
   */
  static function dateTimeFromAD($dateAD) {
    return preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})\.0Z/", '$1-$2-$3 $4:$5:$6', $dateAD);
  }
}
