<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CRecordSante400 {
  static $dbh = null;
  static $chrono = null;
  static $verbose = false;
  static $consumeUnsets = true;
 
  // Fake data source for chrono purposes
  static $ds = null;
  
  
  public $data = array();
  public $valuePrefix = "";
  
  
  function __construct() {
  }

  /**
   * Connect to a AS400 DB2 SQL server via ODBC driver
   * 
   * @throws Error on misconfigured or anavailable server
   * 
   * @return void
   */
  static function connect() {
    if (self::$dbh) {
      return;
    }

    $config = CAppUI::conf("sante400");
    
    if (null == $dsn = $config["dsn"]) {
      trigger_error("Data Source Name not defined, please configure module", E_USER_ERROR);
      CApp::rip();
    }
    
    // Fake data source for chrono purposes
    CSQLDataSource::$dataSources[$dsn] = new CMySQLDataSource();
    $ds =& CSQLDataSource::$dataSources[$dsn];
    $ds->dsn = $dsn;

    self::$chrono =& CSQLDataSource::$dataSources[$dsn]->chrono;
    self::$chrono->start();
    
    $prefix = $config["prefix"];
    self::$dbh = new PDO("$prefix:$dsn", $config["user"], $config["pass"]);
    self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    self::$chrono->stop("connection");
  }

  /**
   * Trace a query applying syntax coloring
   * 
   * @param object $query             Query to execute
   * @param object $values [optional] Values to prepare
   * 
   * @return void
   */
  static function traceQuery($query, $values = array()) {
    // Verbose
    if (!self::$verbose) {
      return;
    }
    
    // Inject values into query
    foreach ($values as $_value) {
      $_value = str_replace("'", "\\'", $_value);
      $query = preg_replace("/\?/", "'$_value'", $query, 1);
    }
    
    echo utf8_decode(CMbString::highlightCode("sql", $query, false, "white-space: pre-wrap;"));
  }
  
  /**
   * Prepare, execute a query and return multiple records
   * 
   * @param object $query             Query to execute
   * @param object $values [optional] Values to prepare
   * @param object $max    [optional] Maximum records returned
   * @param object $class  [optional] Records specific class instances
   * 
   * @return array
   */
  static function loadMultiple($query, $values = array(), $max = 100, $class = "CRecordSante400") {
    if (!new $class instanceof CRecordSante400) {
      trigger_error("instances of '$class' are not instances of 'CRecordSante400'", E_USER_WARNING);
    }
    
    $records = array();
    try {
      self::traceQuery($query, $values);
      self::connect();

      // Query execution
      $sth = self::$dbh->prepare($query);
      self::$chrono->start();
      $sth->execute($values);
      self::$chrono->stop("multiple load execute");

      // Fetching results
      self::$chrono->start();
      while ($data = $sth->fetch(PDO::FETCH_ASSOC) and $max--) {
        self::$chrono->stop("multiple load fetch");
        $record = new $class;
        $record->data = $data;
        $records[] = $record;
        self::$chrono->start();
      }
      self::$chrono->stop();

    } 
    catch (PDOException $e) {
      trigger_error("Error querying '$query' : " . $e->getMessage(), E_USER_ERROR);
    }
    
    return $records;
  }
  
  /**
   * Prepare and execute query
   * 
   * @param object $query             Query to execute
   * @param object $values [optional] Values to prepare against
   *
   * @return int the number of affected rows (-1 for SELECTs), false on error;
   */
  function query($query, $values = array()) {
    try {
      self::traceQuery($query, $values);
      self::connect();

      // Query execution and fetching
      $sth = self::$dbh->prepare($query);
      self::$chrono->start();
      $sth->execute($values);
      $this->data = $sth->fetch(PDO::FETCH_ASSOC);
      self::$chrono->stop("query");
    } 
    catch (PDOException $e) {
      // Fetch throws this exception in case of UPDATE or DELETE query
      if ($e->getCode() == 24000) {
        self::$chrono->stop("query");
        return $sth->rowCount($values);
      }
  
      trigger_error("Error querying '$query' : " . $e->getMessage(), E_USER_ERROR);
      return false;
    }
  }
  
  /**
   * Load a unique record from query
   * @throws Exception if no record fount
   * 
   * @param object $query             Query to execute
   * @param object $values [optional] Values to prepare against
   *
   * @return int the number of affected rows (-1 for SELECTs);
   */
  function loadOne($query, $values = array()) {
    $this->query($query, $values);
    if (!$this->data) {
      $values = join($values, ",");
      throw new Exception("Couldn't find row for query '$query' with values [$values]");
    }
  }
    
  /**
   * Consume a AS400 DDMMYYYY date and turn it into a SQL ISO date 
   * 
   * @param string $valueName DDMMYYYY date value name
   * 
   * @return string ISO date, null on wrong format
   */
  function consumeDateInverse($valueName) {
    $date = $this->consume($valueName);
    
    $reg = "/(\d{1,2})(\d{2})(\d{4})/i";
    
    // Check format anyway
    if (!preg_match($reg, $date)) {
      return null;
    }

    return preg_replace($reg, "$3-$2-$1", $date);
  }
  
   /**
   * Consume and return any value 
   * 
   * @param string $valueName Value name
   * 
   * @return string Trimmed and slashed value
   */
  function consume($valueName) {
    $valueName = "$this->valuePrefix$valueName";
    
    if (!is_array($this->data)) {
      throw new Exception("The value '$valueName' doesn't exist in this record, which has NO value");
    }

    if (!array_key_exists($valueName, $this->data)) {
      throw new Exception("The value '$valueName' doesn't exist in this record");
    }
    
    $value = $this->data[$valueName];
    
    if (self::$consumeUnsets) {
      unset($this->data[$valueName]);
    }

    return trim(addslashes($value));    
  }
  
  /**
   * Lookup any value 
   * 
   * @param string $valueName Value name
   * 
   * @return string Trimmed and slashed value, null if no value
   */
  function lookup($valueName) {
    $valueName = "$this->valuePrefix$valueName";
    
    if (!is_array($this->data)) {
      throw new Exception("Record has NO value, looking up for '$valueName'");
    }

    if (!array_key_exists($valueName, $this->data)) {
      return null;
    }
    
    $value = $this->data[$valueName];
    
    return trim(addslashes($value));    
  }
  

  /**
   * Consume and return phone number value
   * Escaping any non-digit character 
   * 
   * @param string $valueName Value name
   * 
   * @return string 10-digit phone number
   */
  function consumeTel($valueName) {
    $value = $this->consume($valueName);
    $value = preg_replace("/(\D)/", "", $value);
    if ($value) {
      $value = str_pad($value, 10, "0", STR_PAD_LEFT);
    }
    return $value;
  }


  /**
   * Consume and assemble two values with a new line separator
   * Escaping any non-digit character 
   * 
   * @param string $valueName1 Value name 1
   * @param string $valueName2 Value name 2
   * 
   * @return string Multi-line value
   */
  function consumeMulti($valueName1, $valueName2) {
    $value1 = $this->consume($valueName1);
    $value2 = $this->consume($valueName2);
    return $value2 ? "$value1\n$value2" : "$value1";    
  }

  /**
   * Consume a AS400 YYYYMMDD date and turn it into a SQL ISO date 
   * 
   * @param string $valueName YYYYMMDD date value name
   * 
   * @return string ISO date, null on wrong format
   */
  function consumeDate($valueName) {
    $date = $this->consume($valueName);
    if ($date == "0" || $date == "99999999") {
      return null;
    }
    
    $reg = "/(\d{4})(\d{2})(\d{2})/i";
    
    // Check format anyway
    if (!preg_match($reg, $date)) {
      return null;
    }

    return preg_replace($reg, "$1-$2-$3", $date);
  }

  /**
   * Consume a AS400 HHhMM or HHMM time and turn it into a SQL HH:MM:00 time 
   * 
   * @param string $valueName HHhMM or HHMM time value name
   * 
   * @return string HH:MM:00 time
   */
  function consumeTime($valueName) {
    $time = $this->consume($valueName);
    if ($time === "0" ||  $time == "9999") {
      return null;
    }
        
    $time = str_pad($time, 4, "0", STR_PAD_LEFT);

    $reg = "/(\d{2})h?(\d{2})/i";
    $array = array();
    if (!preg_match($reg, $time, $array)) {
      return null;
    }    
    
    // Escape crazy values
    $h = str_pad($array[1] % 24, 2, "0", STR_PAD_LEFT);
    $m = str_pad($array[2] % 60, 2, "0", STR_PAD_LEFT);
    return "$h:$m:00";
  }

  /**
   * Consume a AS400 HH[MM[SS]] flat time and turn it into a SQL ISO time
   * 
   * @param string $valueName HH[MM[SS]] flat time value name
   * 
   * @return string ISO time
   */
  function consumeTimeFlat($valueName) {
    $time = $this->consume($valueName);
    if ($time === "0") {
      return null;
    }

    $time = str_pad($time, 6, "0", STR_PAD_LEFT);
    
    $reg = "/(\d{2})(\d{2})(\d{2})/i";
    return preg_replace($reg, "$1:$2:$3", $time);
  }

  /**
   * Consume and assemble AS400 date and flat time into an SQL ISO datetime 
   * 
   * @param string $dateName YYYYMMDD date time value name
   * @param string $timeName HHhMM or HHMM time value name
   * 
   * @return string ISO datetime
   */
  function consumeDateTime($dateName, $timeName) {
    if (null == $date = $this->consumeDate($dateName)) {
      return null;
    }

    if (null == $time = $this->consumeTime($timeName)) {
      $time = "00:00:00";
    }
    
    return "$date $time";
  }

  /**
   * Consume and assemble AS400 date and flat time into an SQL ISO datetime 
   * 
   * @param string $dateName YYYYMMDD date time value name
   * @param string $timeName HH[MM[SS]] flat time value name
   * 
   * @return string ISO datetime
   */
  function consumeDateTimeFlat($dateName, $timeName) {
    if (null == $date = $this->consumeDate($dateName)) {
      return null;
    }

    if (null == $time = $this->consumeTimeFlat($timeName)) {
      $time = "00:00:00";
    }
    
    return "$date $time";
  }
}
?>
