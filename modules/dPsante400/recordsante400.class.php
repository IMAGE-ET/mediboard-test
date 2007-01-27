<?php

$dPconfig["dPsante400"] = array (
  "dsn" => "ecap",
  "user" => "MEDIBOARD",
  "pass" => "MEDIBOARD",
);

class CRecordSante400 {
  static $dbh = null;
  static $chrono = null;
 
  public $data = array();
  public $valuePrefix = "";
  
  function __construct() {
  }

  function connect() {
    
    if (self::$dbh) {
      return;
    }

    global $dPconfig;
    $dsnConfig = $dPconfig["dPsante400"];
    $dsn = $dsnConfig["dsn"];
    
    global $dbChronos;
    $dbChronos[$dsn] = new Chronometer;
    self::$chrono =& $dbChronos[$dsn];
    self::$chrono->start();
    self::$dbh = new PDO("odbc:$dsn", $dsnConfig["user"], $dsnConfig["pass"]);
    self::$chrono->stop();
  }

  function multipleLoad($sql, $values = array(), $max = 100, $class = "CRecordSante400") {
    if (!is_a(new $class, "CRecordSante400")) {
      trigger_error("instances of '$class' are not instances of 'CRecordSante400'", E_USER_WARNING);
    }
    
    $records = array();
    try {
      self::connect();
      if (null == $sth = self::$dbh->prepare($sql)) {
        throw new PDOException("Couldn't prepare query");
      }
      
      self::$chrono->start();
      $sth->execute($values);
      self::$chrono->stop();

      self::$chrono->start();
      while ($data = $sth->fetch(PDO::FETCH_ASSOC) and $max--) {
        self::$chrono->stop();
        $record = new $class;
        $record->data = $data;
        $records[] = $record;
        self::$chrono->start();
      }
      self::$chrono->stop();

    } catch (PDOException $e) {
      trigger_error("Error querying '$sql' : " . $e->getMessage(), E_USER_ERROR);
    }
    
    return $records;
  }
  
  function query($sql, $values = array()) {
    try {
      self::connect();
      if (null == $sth = self::$dbh->prepare($sql)) {
        throw new PDOException("Couldn't prepare query");
      }
      
      self::$chrono->start();
      $sth->execute($values);
      $this->data = $sth->fetch(PDO::FETCH_ASSOC);
      self::$chrono->stop();
    } catch (PDOException $e) {
      trigger_error("Error querying '$sql' : " . $e->getMessage(), E_USER_ERROR);
    }
  }
  
  function loadOne($sql, $values = array()) {
    $this->query($sql, $values);
    if (!$this->data) {
      $values = join($values, ",");
      throw new Exception("Couldn't find row for query '$sql' with values [$values]");
    }
  }
  
  /**
   * Transforms a DDMMYYYY AS400 date into a YYYY-MM-DD SQL date 
   */
  function consumeDateInverse($valueName) {
    $date = $this->consume($valueName);
    return preg_replace("/(\d{1,2})(\d{2})(\d{4})/i", "$3-$2-$1", $date);
  }
  
  function consume($valueName) {
    $valueName = "$this->valuePrefix$valueName";
    
    if (!is_array($this->data)) {
      throw new Exception("The value '$valueName' doesn't exist in this record, which has NO value");
    }

    if (!array_key_exists($valueName, $this->data)) {
      throw new Exception("The value '$valueName' doesn't exist in this record");
    }
    
    $value = $this->data[$valueName];
    unset($this->data[$valueName]);
    return trim($value);    
  }

  function consumeTel($valueName) {
    $value = $this->consume($valueName);
    $value = preg_replace("/(\D)/", "", $value);
    if ($value) {
      $value = str_pad($value, 10, "0", STR_PAD_LEFT);
    }
    return $value;
  }

  function consumeMulti($valueName1, $valueName2) {
    $value1 = $this->consume($valueName1);
    $value2 = $this->consume($valueName2);
    return $value2 ? "$value1\n$value2" : "$value1";    
  }

  /**
   * Transforms a YYYYMMDD AS400 date into a YYYY-MM-DD SQL date 
   */
  function consumeDate($valueName) {
    $date = $this->consume($valueName);
    if ($date == "0" or $date == "99999999") {
      return null;
    }
    
    $reg = "/(\d{4})(\d{2})(\d{2})/i";
    return preg_replace($reg, "$1-$2-$3", $date);
  }

  function consumeTime($valueName) {
    $time = $this->consume($valueName);
    if (!$time === "0") {
      return null;
    }
    
    if (strlen($time) == 2) {
      $time = "00" . $time ;
    }
    
    $reg = "/(\d{0,2})h?(\d{2})/i";
    return preg_replace($reg, "$1:$2:00", $time);
  }

  function consumeDateTime($dateName, $timeName) {
    $date = $this->consumeDate($dateName);

    if (null == $time = $this->consumeTime($timeName)) {
      $time = "00:00:00";
    }
    
    return $time ? "$date $time" : $date;
  }
}
?>
