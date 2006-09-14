<?php

global $AppUI;

require_once($AppUI->getModuleClass("dPsante400", "idsante400"));

$etablissements = array(
  "310" => "St Louis",
  "474" => "Sauvegarde",
  "927" => "clinique du Tonkin"
);

class CMouvSejourTonkin {
  var $dbh = null;
  var $base = "GT_EAI";
  var $table = "SEJMDB";
  
  var $sejour = null;
  var $etablissement = null;
  
  function __construct() {
  }
  
  function load() {
    $sql = "SELECT * FROM $this->base.$this->table";
    foreach ($this->query($sql) as $key => $value) {
      $this->$key = $value;
    }
    
    mbTrace($this, "Mouvement de séjour");
  }
  
  function proceed() {
    $sejour = new CSejour;
    
    $this->etablissement = new CGroups;
    $etp01 = $this->query("SELECT * FROM PICLIN$this->CODETB.ETP01");
    mbTrace($etp01);
    $this->etablissement->text = $etp01["ETRSOC"];

    $id400Etab = new CIdSante400();
    $id400Etab->bindObject($this->etablissement, $this->CODETB);
  }
  
  function query($sql) {
    $dsn = "sante400";
    
    try {
      if (!$this->dbh) {
        $this->dbh = new PDO("odbc:$dsn", "", "");
      }
      $sth = $this->dbh->prepare($sql);
      $sth->execute();
      return $sth->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      trigger_error("Error querying '$sql' on data source name '$dsn' ! : " . $e->getMessage(), E_USER_ERROR);
    }    
  }
}
?>
