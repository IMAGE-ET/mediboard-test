<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CPlageconsult extends CMbObject {
  static $minutes = array();
  static $hours = array();
  static $hours_start = null;  
  static $hours_stop = null;  
  // DB Table key
  var $plageconsult_id = null;

  // DB References
  var $chir_id = null;

  // DB fields
  var $date    = null;
  var $freq    = null;
  var $debut   = null;
  var $fin     = null;
  var $libelle = null;

  // Form fields
  var $_hour_deb = null;
  var $_min_deb  = null;
  var $_hour_fin = null;
  var $_min_fin  = null;
  var $_freq     = null;
  var $_affected = null;
  var $_total    = null;
  var $_fill_rate= null;

  // Object References
  var $_ref_chir          = null;
  var $_ref_consultations = null;

  function CPlageconsult() {
    $this->CMbObject("plageconsult", "plageconsult_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["0"] = "CConsultation plageconsult_id";
     return $backRefs;
  }

  function getSpecs() {
    return array (
      "chir_id" => "notNull ref class|CMediusers",
      "date"    => "notNull date",
      "freq"    => "notNull time",
      "debut"   => "notNull time",
      "fin"     => "notNull time",
      "libelle" => "str"
    );
  }
  
  function getSeeks() {
    return array (
      "chir_id" => "ref|CMediusers",
      "libelle" => "like"
    );
  }
  
  function loadRefsBack($withCanceled = true) {
    if (!$withCanceled) {
      $where["annule"] = "= '0'";
    }
    
    $where["plageconsult_id"] = "= '$this->plageconsult_id'";
    $order = "heure";

    $this->_ref_consultations = new CConsultation();
    $this->_ref_consultations = $this->_ref_consultations->loadList($where, $order);

    $this->loadFillRate();
  }
  
  function loadFillRate() {
    if (!$this->_id) {
      return;
    }

    $query = "SELECT SUM(duree) " .
      "\nFROM `consultation` " .
      "\nWHERE `plageconsult_id` = $this->_id" .
      "\nAND `consultation`.`patient_id` IS NOT NULL && `annule` = '0'";
      
    $this->_affected = intval(db_loadResult($query));

    if ($this->_total) {
      $this->_fill_rate= round($this->_affected/$this->_total*100);
    }
  }
  
  function loadRefs($withCanceled = true) {
    $this->loadRefsFwd();
    $this->loadRefsBack($withCanceled);
  }
  
  function loadRefsFwd() {
    $this->_ref_chir = new CMediusers();
    $this->_ref_chir->load($this->chir_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir) {
      $this->loadRefsFwd();
    }
    return $this->_ref_chir->getPerm($permType);
  }

  function checkFrequence() {
  	return true;

  	$oldValues = new CPlageconsult();
  	$oldValues->load($this->plageconsult_id);
  	$oldValues->loadRefs();

	  return $oldValues->_freq == $this->_freq 
      or count($oldValues->_ref_consultations) == 0;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "consultations", 
      "name"      => "consultation",
      "idfield"   => "consultation_id", 
      "joinfield" => "plageconsult_id"
    );
    return parent::canDelete( $msg, $oid, $tables );
  }

/*
 * returns collision message, null for no collision
 */
  function hasCollisions() {
    // Get all other plages the same day
    $where["chir_id"]         = "= '$this->chir_id'";
    $where["date"]            = "= '$this->date'";
    $where["plageconsult_id"] = "!= '$this->plageconsult_id'";
    $plages = new CPlageconsult;
    $plages = $plages->loadList($where);

    //mbTrace(count($plages), "Nombre de plages avec des collisions possibles");
    $msg = null;
    
    foreach ($plages as $plage) {
      if (($plage->debut <  $this->fin   and $plage->fin >  $this->fin  )
        or($plage->debut <  $this->debut and $plage->fin >  $this->debut)
        or($plage->debut >= $this->debut and $plage->fin <= $this->fin  )) {
        $msg .= "Collision avec la plage du $this->date, de $plage->debut à $plage->fin.";
      }
    }
    
    return $msg;
  }

  function check() {
    // Data checking
    $msg = null;

    if(!$this->plageconsult_id) {
      if (!$this->chir_id) {
        $msg .= "Praticien non valide<br />";
      }
    }

    return $msg . parent::check();
  }
  
  function store() {
    $this->updateDBFields();
    
    if ($msg = $this->hasCollisions()) {
      return $msg;
    }

    if ($this->plageconsult_id) {
      if (!$this->checkFrequence()) {
        return "Vous ne pouvez pas modifier la fréquence de cette plage";
      }
    }

    return parent::store();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_hour_deb = intval(substr($this->debut, 0, 2));
    $this->_min_deb  = intval(substr($this->debut, 3, 2));
    $this->_hour_fin = intval(substr($this->fin, 0, 2));
    $this->_min_fin  = intval(substr($this->fin, 3, 2));
    $this->_freq     = substr($this->freq, 3, 2);
    $tmpHfin         = substr($this->fin, 0, 2);
    $tmpMfin         = substr($this->fin, 3, 2);
    $tmpHdebut       = substr($this->debut, 0, 2);
    $tmpMdebut       = substr($this->debut, 3, 2);
    $tmpfreq         = 60 / substr($this->freq, 3, 2);
    $this->_total    = (($tmpHfin + $tmpMfin/60) - ($tmpHdebut + $tmpMdebut/60)) * $tmpfreq;    
  }
  
  function updateDBFields() {
  	if($this->_hour_deb !== null) {
      if($this->_min_deb !== null)
        $this->debut = $this->_hour_deb.":".$this->_min_deb.":00";
      else
        $this->debut = $this->_hour_deb.":00:00";
    }
    if($this->_hour_fin !== null) {
      if($this->_min_fin !== null)
        $this->fin = $this->_hour_fin.":".$this->_min_fin.":00";
      else
        $this->fin = $this->_hour_fin.":00:00";
    }
    if ($this->_freq !== null)
      $this->freq  = "00:". $this->_freq. ":00";
  }
  
  function becomeNext() {
    // Store form fields
    $_hour_deb = $this->_hour_deb;
    $_min_deb  = $this->_min_deb;
    $_hour_fin = $this->_hour_fin;
    $_min_fin  = $this->_min_fin;
    $_freq     = $this->_freq;
    $libelle   = $this->libelle;

    $this->date = mbDate("+7 DAYS", $this->date);
    $where["date"] = "= '$this->date'";
    $where["chir_id"] = "= '$this->chir_id'";
    $where[] = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    if (!$this->loadObject($where)) {
      $this->plageconsult_id = null;
    }

    // Restore form fields
    $this->_hour_deb = $_hour_deb;
    $this->_min_deb  = $_min_deb;
    $this->_hour_fin = $_hour_fin;
    $this->_min_fin  = $_min_fin;
    $this->_freq     = $_freq;
    $this->libelle   = $libelle;
    $this->updateDBFields();
  }    
}

global $dPconfig;
$pcConfig =& $dPconfig["dPcabinet"]["CPlageconsult"];

CPlageconsult::$hours_start = $pcConfig["hours_start"];
CPlageconsult::$hours_stop  = $pcConfig["hours_stop"];
CPlageconsult::$hours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
CPlageconsult::$minutes = range(0, 59, $pcConfig["minutes_interval"]);
?>