<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author Romain Ollivier
 */

class CCodeCIM10 {
  const LANG_FR = "FR_OMS";
  const LANG_EN = "EN_OMS";
  const LANG_DE = "GE_DIMDI";

  var $_spec = null;
  
  // Lite props
  var $code    = null;
  var $sid     = null;
  var $level   = null;
  var $libelle = null;
  var $exist   = null;
  
  // Others props
  var $descr     = null;
  var $glossaire = null;
  var $include   = null;
  var $indir     = null;
  var $notes     = null;
  
  // Références
  var $_exclude   = null;
  var $_levelsSup = null;
  var $_levelsInf = null;

  // Langue
  var $_lang = null;
  
  // Other
  var $_isInfo = null;
  
  /**
   * Construction
   */
  function CCodeCIM10($code = "(A00-B99)", $loadlite = 0) {
    // Static initialisation
    static $spec = null;
    if (!$spec) {
      $spec = new CMbObjectSpec();
      $spec->dsn = "cim10";
      $spec->init();
    }
    
    $this->_spec =& $spec;

    $this->code = strtoupper($code);
    
    if ($loadlite) {
      $this->loadLite();
    }
  }
  
  // Chargement des données Lite
  function loadLite($lang = self::LANG_FR) {
    $this->exist = true;
    $ds =& $this->_spec->ds;
    
    $this->_lang = $lang;

    // Vérification de l'existence du code
    $query = "SELECT COUNT(abbrev) AS total
              FROM master
              WHERE abbrev = '$this->code'";
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    if ($row["total"] == 0) {
      $this->libelle = "Code CIM inexistant";
      $this->exist = false;
      return false;
    }
    // sid
    $query = "SELECT SID
              FROM master
              WHERE abbrev = '$this->code'";
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->sid = $row["SID"];
    
    // code et level
    $query = "SELECT abbrev, level
              FROM master
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->code = $row["abbrev"];
    $this->level = $row["level"];
    
    //libelle
    $query = "SELECT $this->_lang
              FROM libelle
              WHERE SID = '$this->sid'
              AND source = 'S'";
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->libelle = $row[$this->_lang];
    return true;
  }
  
  // Chargement des données
  function load($lang = self::LANG_FR) {
    if (!$this->loadLite($lang)){
      return false;
    }
    $ds =& $this->_spec->ds;
    //descr
    $this->descr = array();
    $query = "SELECT LID
              FROM descr
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT $this->_lang
                FROM libelle
                WHERE LID = '".$row["LID"]."'";
      $result2 = $ds->exec($query);
      if ($row2 = $ds->fetchArray($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->descr) === false) {
          $this->descr[] = $found;
        }
      }
    }
    
    // glossaire
    $this->glossaire = array();
    $query = "SELECT MID
              FROM glossaire
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT $this->_lang
                FROM memo
                WHERE MID = '".$row["MID"]."'";
      $result2 = $ds->exec($query);
      if ($row2 = $ds->fetchArray($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->glossaire) === false) {
          $this->glossaire[] = $found;
        }
      }
    }

    //include
    $this->include = array();
    $query = "SELECT LID
              FROM include
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT $this->_lang
                FROM libelle
                WHERE LID = '".$row["LID"]."'";
      $result2 = $ds->exec($query);
      if ($row2 = $ds->fetchArray($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->include) === false) {
          $this->include[] = $found;
        }
      }
    }

    //indir
    $this->indir = array();
    $query = "SELECT LID
              FROM indir
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT $this->_lang
                FROM libelle
                WHERE LID = '".$row["LID"]."'";
      $result2 = $ds->exec($query);
      if ($row2 = $ds->fetchArray($result2)) {
        $found = $row2[$this->_lang];
        if ($found != "" and array_search($found, $this->indir) === false) {
          $this->indir[] = $found;
        }
      }
    }
  
    //notes
    $this->notes = array();
    $query = "SELECT MID
              FROM note
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT $this->_lang
                FROM memo
                WHERE MID = '".$row["MID"]."'";
      $result2 = $ds->exec($query);
      if ($row2 = $ds->fetchArray($result2)) {
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
    return true;
  }
  
  function loadRefs() {
    
    if (!$this->loadLite($this->_lang, 0)){
      return false;
    }
    
    $ds =& $this->_spec->ds;
    
    // Exclusions
    $this->_exclude = array();
    $query = "SELECT LID, excl
              FROM exclude
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    while ($row = $ds->fetchArray($result)) {
      $query = "SELECT abbrev
                FROM master
                WHERE SID = '".$row["excl"]."'";
      $result2 = $ds->exec($query);
      if ($row2 = $ds->fetchArray($result2)) {
        $code_cim10 = $row2["abbrev"];
        if (array_key_exists($code_cim10, $this->_exclude) === false) {
          $code = new CCodeCIM10($code_cim10);
          $code->loadLite($this->_lang, 0);
          $this->_exclude[$code_cim10] = $code;
        }
      }
    }
    ksort($this->_exclude);
    
    // Arborescence
    $query = "SELECT *
              FROM master
              WHERE SID = '$this->sid'";
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);

    // Niveaux superieurs
    $this->_levelsup = array();
    for ($index = 1; $index <= 7; $index++) {
      $code_cim10_id = $row["id$index"];
      if ($code_cim10_id) {
        $query = "SELECT abbrev
                  FROM master
                  WHERE SID = '$code_cim10_id'";
        $result = $ds->exec($query);
        $row2 = $ds->fetchArray($result);
        $code_cim10 = $row2["abbrev"];
        $code = new CCodeCIM10($code_cim10);
        $code->loadLite($this->_lang, 0);
        $this->_levelsSup[$index] = $code;
      }
    }

    // Niveaux inferieurs
    $this->_levelsInf = array();
    $query = "SELECT *
              FROM master
              WHERE id$this->level = '$this->sid'
              AND id".($this->level+1)." != '0'";
    if ($this->level < 6) {
      $query .= "\nAND id".($this->level+2)." = '0'";
    }
    
    $result = $ds->exec($query);
    while ($row = $ds->fetchArray($result)) {
      $code_cim10 = $row["abbrev"];
      $code = new CCodeCIM10($code_cim10);
      $code->loadLite($this->_lang, 0);
      $this->_levelsInf[$code_cim10] = $code;
    }
    
    ksort($this->_levelsInf);
}
  
  // Sommaire
  function getSommaire($lang = self::LANG_FR, $level = 0) {
    $ds =& $this->_spec->ds;
    $this->_lang = $lang;

    $query = "SELECT * FROM chapter ORDER BY chap";
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $chapter[$i]["rom"] = $row["rom"];
      $query = "SELECT * FROM master WHERE SID = '".$row["SID"]."'";
      $result2 = $ds->exec($query);
      $row2 = $ds->fetchArray($result2);
      $chapter[$i]["code"] = $row2["abbrev"];
      $query = "SELECT * FROM libelle WHERE SID = '".$row["SID"]."' AND source = 'S'";
      $result2 = $ds->exec($query);
      $row2 = $ds->fetchArray($result2);
      $chapter[$i]["text"] = $row2[$this->_lang];
      $i++;
    }
  
    return ($chapter);
  }
  
  // Recherche de codes
  function findCodes($code, $keys, $lang = self::LANG_FR) {
    $ds =& $this->_spec->ds;
    $this->_lang = $lang;
  
    $query = "SELECT libelle.$this->_lang AS $this->_lang, master.abbrev AS abbrev
              FROM libelle, master
              WHERE libelle.SID = master.SID";
    $hasWhere = false;
    $keywords = explode(" ", $keys);
    if($keys != "") {
      $listLike = array();
      foreach($keywords as $key => $value) {
        $listLike[] = "libelle.$lang LIKE '%".addslashes($value)."%'";
      }
      $query .= " AND ".implode(" AND ", $listLike);
      $hasWhere = true;
    }
    if($code) {
      $query .= " AND master.abbrev LIKE '$code%'";
      $hasWhere = true;
    }
    if(!$hasWhere) {
      $query .= " AND 0";
    }
    $query .= " ORDER BY master.SID LIMIT 0 , 100";
    $result = $ds->exec($query);
    $master = array();
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $master[$i]["text"] = $row[$this->_lang];
      $master[$i]["code"] = $row["abbrev"];
      $i++;
    }
  
    return($master);
  }
  
  function getSubCodes($code) {
    $codeCim = new CCodeCIM10($code);
    $codeCim->load();
    $codeCim->loadRefs();
    $master = array();
    $i = 0;
    foreach($codeCim->_levelsInf as $curr_code) {
      $master[$i]["text"] = $curr_code->libelle;
      $master[$i]["code"] = $curr_code->code;
      $i++;
    }
    return $master;
  }
  
  static function addPoint($code) {
    if(!strpos($code, ".") && strlen($code) >= 4) {
      $code = substr($code, 0, 3).".".substr($code, 3);
    }
    return $code;
  }

}

?>