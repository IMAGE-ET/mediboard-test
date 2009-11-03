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
  static $minutes_interval = null;
  
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
  var $_hour_deb  = null;
  var $_min_deb   = null;
  var $_hour_fin  = null;
  var $_min_fin   = null;
  var $_freq      = null;
  var $_affected  = null;
  var $_total     = null;
  var $_fill_rate = null;
  var $_nb_patients = null;
  
  // Filter fields
  var $_date_min = null;
  var $_date_max = null;
  var $_function_id = null;
  
  // Object References
  var $_ref_chir          = null;
  var $_ref_consultations = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plageconsult';
    $spec->key   = 'plageconsult_id';
    return $spec;
  }
  
  function getBackProps() {
      $backProps = parent::getBackProps();
      $backProps["consulations"] = "CConsultation plageconsult_id";
     return $backProps;
  }

  function getProps() {
    $parentSpecs = parent::getProps();
    $specs = array (
      "chir_id" => "ref notNull class|CMediusers seekable",
      "date"    => "date notNull",
      "freq"    => "time notNull",
      "debut"   => "time notNull",
      "fin"     => "time notNull",
      "libelle" => "str seekable",

      // Form fields
      "_hour_deb"  => "",
      "_min_deb"   => "",
      "_hour_fin"  => "",
      "_min_fin"   => "",
      "_freq"      => "",
      "_affected"  => "",
      "_total"     => "",
      "_fill_rate" => "",
      
      // Filter fields
      "_date_min"    => "date",
      "_date_max"    => "date moreThan|_date_min",
      "_function_id" => "ref class|CFunctions",
      );

    return array_merge($parentSpecs, $specs);
  }
  
  /**
   * Load consultations
   * @param bool $withCanceled Include cancelled consults
   * @param bool $withClosed Include closed consults
   */
  function loadRefsConsultations($withCanceled = true, $withClosed = true) {
    $where["plageconsult_id"] = "= '$this->_id'";
    
    if (!$withCanceled) {
      $where["annule"] = "= '0'";
    }

    if (!$withClosed) {
      $where["chrono"] = "!=  '" . CConsultation::TERMINE . "'";   
    }
    
    $order = "heure";
    $consult = new CConsultation();
    $this->_ref_consultations = $consult->loadList($where, $order);
  }
  
  function countPatients(){
    $consultation = new CConsultation();
    $consultation->plageconsult_id = $this->_id;
    $where["plageconsult_id"] = "= '$this->_id'";
    $where["patient_id"] = " IS NOT NULL";
    $this->_nb_patients = $consultation->countList($where);
  }
  
  
  function loadRefsBack($withCanceled = true) {
    $this->loadRefsConsultations($withCanceled);
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
      
    $this->_affected = intval($this->_spec->ds->loadResult($query));

    if ($this->_total) {
      $this->_fill_rate= round($this->_affected/$this->_total*100);
    }
  }
  
  function loadRefs($withCanceled = true, $cache = 0) {
    $this->loadRefsFwd($cache);
    $this->loadRefsBack($withCanceled);
  }
  
  function loadRefsFwd($cache = 0) {
    $this->_ref_chir = new CMediusers();
    if($cache) {
      $this->_ref_chir = $this->_ref_chir->getCached($this->chir_id);
    } else {
      $this->_ref_chir->load($this->chir_id);
    }
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir) {
      $this->loadRefsFwd(1);
    }
    return $this->_ref_chir->getPerm($permType) && $this->_ref_module->getPerm($permType);
  }

  function checkFrequence() {
  	return true;

  	$oldValues = new CPlageconsult();
  	$oldValues->load($this->plageconsult_id);
  	$oldValues->loadRefs();

	  return $oldValues->_freq == $this->_freq 
      or count($oldValues->_ref_consultations) == 0;
  }
  
/*
 * returns collision message, null for no collision
 */
  function hasCollisions() {
    $this->completeField("date");
    
    // Get all other plages the same day
    $where["chir_id"]         = "= '$this->chir_id'";
    $where["date"]            = "= '$this->date'";
    $where["plageconsult_id"] = "!= '$this->plageconsult_id'";
    $plages = new CPlageconsult;
    $plages = $plages->loadList($where);
    
    $msg = null;
    
    $this->completeField("debut");
    $this->completeField("fin");
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

$pcConfig = CAppUI::conf("dPcabinet CPlageconsult");

/*
CPlageconsult::$hours_start = $pcConfig["hours_start"];
CPlageconsult::$hours_stop  = $pcConfig["hours_stop"];
CPlageconsult::$hours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
CPlageconsult::$minutes = range(0, 59, $pcConfig["minutes_interval"]);
*/

CPlageconsult::$hours_start = str_pad(CValue::first($pcConfig["hours_start"], "08"),2,"0",STR_PAD_LEFT);
CPlageconsult::$hours_stop  = str_pad(CValue::first($pcConfig["hours_stop"], "20"),2,"0",STR_PAD_LEFT);
CPlageconsult::$minutes_interval = CValue::first($pcConfig["minutes_interval"],"15");


$hours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
$mins  = range(0, 59, CPlageconsult::$minutes_interval);

foreach($hours as $key => $hour){
	CPlageconsult::$hours[$hour] = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

foreach($mins as $key => $min){
	CPlageconsult::$minutes[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}

?>