<?php

global $AppUI;

$etablissements = array(
  "310" => "St Louis",
  "474" => "Sauvegarde",
  "927" => "clinique du Tonkin"
);

class CRecordSante400 {
  var $data = array();
  
  function __construct() {
  }

  function query($sql) {
    static $dsn = "sante400";
    static $dbh = null;
    
    try {
      if (!$dbh) {
        $dbh = new PDO("odbc:$dsn", "", "");
      }
      $sth = $dbh->prepare($sql);
      $sth->execute();
      $this->data = $sth->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      trigger_error("Error querying '$sql' on data source name '$dsn' ! : " . $e->getMessage(), E_USER_ERROR);
      throw $e;
    }    
  }
  
  /**
   * Transforms a DDMMYYYY AS400 date into a YYYY-MM-DD SQL date 
   */
  function consumeDateInverse($valueName) {
    $date = $this->consume($valueName);
    return preg_replace("/(\d{2})(\d{2})(\d{4})/i", "$3-$2-$1", $date);
  }
  
  function consume($valueName) {
    $value = $this->data[$valueName];
    unset($this->data[$valueName]);
    return trim($value);    
  }

  function consumeMulti($valueName1, $valueName2) {
    $value1 = $this->consume($valueName1);
    $value2 = $this->consume($valueName2);
    return $value2 ? "$value1\n$value2" : "$value1";    
  }

  function consumeDate($valueName) {
    $date = $this->consume($valueName);
    if ($date == "0") {
      $date = "";
    }
    
    return preg_replace("/(\d{4})(\d{2})(\d{2})/i", "$1-$2-$3", $date);
  }

  function consumeTime($valueName) {
    $time = $this->consume($valueName);
    if ($time == "0") {
      $time = "";
    }

    return preg_replace("/(\d{2})(\d{2})/i", "$1:$2:00", $time);
  }

  function consumeDateTime($dateName, $timeName) {
    $date = $this->consumeDate($dateName);
    $time = $this->consumeTime($timeName);
    return $time ? "$date $time" : $date;
  }
}
?>
