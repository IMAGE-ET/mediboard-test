<?php /* $Id: codecim10.class.php,v 1.6 2006/03/08 13:18:54 rhum1 Exp $ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision: 1.6 $
 * @author Romain Ollivier
 */

// Enum for langages
if(!defined("LANG_FR")) {
  define("LANG_FR", "FR_OMS");
  define("LANG_EN", "EN_OMS");
  define("LANG_DE", "GE_DIMDI");
}

class CCodeCIM10 {

  // Lite props
  var $code = null;
  var $sid = null;
  var $level = null;
  var $libelle = null;
  
  // Others props
  var $descr = null;
  var $glossaire = null;
  var $include = null;
  var $indir = null;
  var $notes = null;
  
  // Références
  var $_exclude = null;
  var $_levelsSup = null;
  var $_levelsInf = null;

  // Langue
  var $_lang = null;
  
  // Other
  var $isInfo = null;
  // Id de la base de données (qui doit être dans le config.php)
  var $dbcim10 = null;
  
  
  // Constructeur
  function CCodeCIM10($code = "(A00-B99)", $loadlite = 0) {
    global $AppUI;
    $this->dbcim10 = $AppUI->cfg['baseCIM10'];
    do_connect($this->dbcim10);
    $this->code = strtoupper($code);
    if($loadlite)
      $this->loadLite();
  }
  
  // Chargement des données Lite
  function loadLite($lang = LANG_FR, $connection = 1) {
    
    $this->_lang = $lang;

    // Vérification de l'existence du code
    $query = "SELECT COUNT(abbrev) AS total" .
        "\nFROM master" .
        "\nWHERE abbrev = '$this->code'";
    $result = db_exec($query, $this->dbcim10);
    $row = db_fetch_array($result);
    if ($row["total"] == 0) {
      $this->code = "(A00-B99)";
    }
    // sid
    $query = "SELECT SID" .
        "\nFROM master" .
        "\nWHERE abbrev = '$this->code'";
    $result = db_exec($query, $this->dbcim10);
    $row = db_fetch_array($result);
    $this->sid = $row['SID'];
    // code et level
    $query = "SELECT abbrev, level" .
        "\nFROM master" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    $row = db_fetch_array($result);
    $this->code = $row['abbrev'];
    $this->level = $row['level'];
    //libelle
    $query = "SELECT $this->_lang" .
        "\nFROM libelle" .
        "\nWHERE SID = '$this->sid'" .
        "\nAND source = 'S'";
    $result = db_exec($query, $this->dbcim10);
    $row = db_fetch_array($result);
    $this->libelle = $row[$this->_lang];
  }
  
  // Chargement des données
  function load($lang = LANG_FR, $connection = 1) {

    $this->loadLite($lang, 0);

    //descr
    $this->descr = array();
    $query = "SELECT LID" .
        "\nFROM descr" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    while($row = db_fetch_array($result)) {
      $query = "SELECT $this->_lang" .
          "\nFROM libelle" .
          "\nWHERE LID = '".$row['LID']."'";
      $result2 = db_exec($query, $this->dbcim10);
      if ($row2 = db_fetch_array($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->descr) === false) {
          $this->descr[] = $found;
        }
      }
    }
    
    // glossaire
    $this->glossaire = array();
    $query = "SELECT MID" .
        "\nFROM glossaire" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    $i = 0;
    while($row = db_fetch_array($result)) {
      $query = "SELECT $this->_lang" .
          "\nFROM memo" .
          "\nWHERE MID = '".$row['MID']."'";
      $result2 = db_exec($query, $this->dbcim10);
      if ($row2 = db_fetch_array($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->glossaire) === false) {
          $this->glossaire[] = $found;
        }
      }
    }

    //include
    $this->include = array();
    $query = "SELECT LID" .
        "\nFROM include" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    while($row = db_fetch_array($result)) {
      $query = "SELECT $this->_lang" .
          "\nFROM libelle" .
          "\nWHERE LID = '".$row['LID']."'";
      $result2 = db_exec($query, $this->dbcim10);
      if ($row2 = db_fetch_array($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->include) === false) {
          $this->include[] = $found;
        }
      }
    }

    //indir
    $this->indir = array();
    $query = "SELECT LID" .
        "\nFROM indir" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    while($row = db_fetch_array($result)) {
      $query = "SELECT $this->_lang" .
          "\nFROM libelle" .
          "\nWHERE LID = '".$row['LID']."'";
      $result2 = db_exec($query, $this->dbcim10);
      if ($row2 = db_fetch_array($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->indir) === false) {
          $this->indir[] = $found;
        }
      }
    }
  
    //notes
    $this->notes = array();
    $query = "SELECT MID" .
        "\nFROM note" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    $i = 0;
    while($row = db_fetch_array($result)) {
      $query = "SELECT $this->_lang" .
          "\nFROM memo" .
          "\nWHERE MID = '".$row['MID']."'";
      $result2 = db_exec($query, $this->dbcim10);
      if ($row2 = db_fetch_array($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->notes) === false) {
          $this->notes[] = $found;
        }
      }
    }
    
    // Is info ?
    $this->_isInfo  = count($this->descr);
    $this->_isInfo += count($this->glossaire);
    $this->_isInfo += count($this->include);
    $this->_isInfo += count($this->indir);
    $this->_isInfo += count($this->notes);
    
  }
  
  function loadRefs($connection = 1) {

    // Exclusions
    $this->_exclude = array();
    $query = "SELECT LID, excl" .
        "\nFROM exclude" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    while ($row = db_fetch_array($result)) {
      $query = "SELECT abbrev" .
          "\nFROM master" .
          "\nWHERE SID = '".$row['excl']."'";
      $result2 = db_exec($query, $this->dbcim10);
      if ($row2 = db_fetch_array($result2)) {
        $code_cim10 = $row2['abbrev'];
        if (array_key_exists($code_cim10, $this->_exclude) === false) {
          $code = new CCodeCIM10($code_cim10);
          $code->loadLite($this->_lang, 0);
          $this->_exclude[$code_cim10] = $code;
        }
      }
    }
    ksort($this->_exclude);
    
    // Arborescence
    $query = "SELECT *" .
        "\nFROM master" .
        "\nWHERE SID = '$this->sid'";
    $result = db_exec($query, $this->dbcim10);
    $row = db_fetch_array($result);

    // Niveaux superieurs
    $this->_levelsup = array();
    for ($index = 1; $index <= 7; $index++) {
    	$code_cim10_id = $row["id$index"];
      if ($code_cim10_id) {
        $query = "SELECT abbrev" .
            "\nFROM master" .
            "\nWHERE SID = '$code_cim10_id'";
        $result = db_exec($query, $this->dbcim10);
        $row2 = db_fetch_array($result);
        $code_cim10 = $row2['abbrev'];
        $code = new CCodeCIM10($code_cim10);
        $code->loadLite($this->_lang, 0);
        $this->_levelsSup[$index] = $code;
      }
    }

    // Niveaux inferieurs
    $this->_levelsInf = array();
    $query = "SELECT *" .
        "\nFROM master" .
        "\nWHERE id$this->level = '$this->sid'" .
        "\nAND id".($this->level+1)." != '0'";
    if ($this->level < 6) {
      $query .= "\nAND id".($this->level+2)." = '0'";
    }
    
    $result = db_exec($query, $this->dbcim10);
    while ($row = db_fetch_array($result)) {
      $code_cim10 = $row['abbrev'];
      $code = new CCodeCIM10($code_cim10);
      $code->loadLite($this->_lang, 0);
      $this->_levelsInf[$code_cim10] = $code;
    }
    
    ksort($this->_levelsInf);
}
  
  // Sommaire
  function getSommaire($lang = LANG_FR, $connection = 1) {
    $this->_lang = $lang;

    $query = "SELECT * FROM chapter ORDER BY chap";
    $result = db_exec($query, $this->dbcim10);
    $i = 0;
    while($row = db_fetch_array($result)) {
      $chapter[$i]["rom"] = $row['rom'];
      $query = "SELECT * FROM master WHERE SID = '".$row['SID']."'";
      $result2 = db_exec($query, $this->dbcim10);
      $row2 = db_fetch_array($result2);
      $chapter[$i]["code"] = $row2['abbrev'];
      $query = "SELECT * FROM libelle WHERE SID = '".$row['SID']."' AND source = 'S'";
      $result2 = db_exec($query, $this->dbcim10);
      $row2 = db_fetch_array($result2);
      $chapter[$i]["text"] = $row2[$this->_lang];
      $i++;
    }
  
    return ($chapter);
  }
  
  // Recherche de codes
  function findCodes($keys, $lang = LANG_FR, $connection = 1) {
    $this->_lang = $lang;
  
    $query = "SELECT * FROM libelle WHERE 0";
    $keywords = explode(" ", $keys);
    if($keys != "") {
      $query .= " OR (1";
      foreach($keywords as $key => $value) {
        $query .= " AND $lang LIKE '%".addslashes($value)."%'";
      }
      $query .= ")";
    }
    $query .= " ORDER BY SID LIMIT 0 , 100";
    $result = db_exec($query, $this->dbcim10);
    $master = array();
    $i = 0;
    while($row = db_fetch_array($result)) {
      $master[$i]['text'] = $row[$this->_lang];
      $query = "SELECT * FROM master WHERE SID = '".$row['SID']."'";
      $result2 =db_exec($query, $this->dbcim10);
      $row2 = db_fetch_array($result2);
      $master[$i]['code'] = $row2['abbrev'];
      $i++;
    }
  
    return($master);
  }

}