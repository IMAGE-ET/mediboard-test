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
  var $remplacant_id = null;
  
  // DB fields
  var $date    = null;
  var $freq    = null;
  var $debut   = null;
  var $fin     = null;
  var $libelle = null;
  var $locked  = null;
  var $remplacant_ok = null;
  var $desistee = null;
  var $color = null;
  
  // Form fields
  var $_freq                 = null;
  var $_affected             = null;
  var $_total                = null;
  var $_fill_rate            = null;
  var $_nb_patients          = null;
  var $_consult_by_categorie = null;
  var $_colliding_plages     = null;
  var $_type_repeat          = null;
  
  // Field pour le calcul de collision (fin à 00:00:00)
  var $_fin = null;
	
  // Filter fields
  var $_date_min = null;
  var $_date_max = null;
  var $_function_id = null;
  var $_other_function_id = null;
  var $_user_id  = null;
  
  // Object References
  var $_ref_chir          = null;
  var $_ref_consultations = null;
  var $_ref_remplacant    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plageconsult';
    $spec->key   = 'plageconsult_id';
    return $spec;
  }
  
  function getBackProps() {
      $backProps = parent::getBackProps();
      $backProps["consultations"] = "CConsultation plageconsult_id";
     return $backProps;
  }

  function getProps() {
    $parentSpecs = parent::getProps();
    $specs = array (
      "chir_id"       => "ref notNull class|CMediusers seekable",
      "remplacant_id" => "ref class|CMediusers seekable",
      "date"          => "date notNull",
      "freq"          => "time notNull",
      "debut"         => "time notNull",
      "fin"           => "time notNull moreThan|debut",
      "libelle"       => "str seekable",
      "locked"        => "bool default|0",
      "remplacant_ok" => "bool default|0",
      "desistee"      => "bool default|0",
      "color"         => "str notNull length|6 default|ffffff",
      
      // Form fields
      "_freq"        => "",
      "_affected"    => "",
      "_total"       => "",
      "_fill_rate"   => "",
      "_type_repeat" => "enum list|simple|double|triple|quadruple|sameweek",
      
      // Filter fields
      "_date_min"          => "date",
      "_date_max"          => "date moreThan|_date_min",
      "_function_id"       => "ref class|CFunctions",
      "_other_function_id" => "ref class|CFunctions",
      "_user_id"           => 'ref class|CMediusers'
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
    return $this->_ref_consultations = $consult->loadList($where, $order);
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

    $query = "SELECT SUM(`consultation`.`duree`)
              FROM `consultation`
              WHERE `consultation`.`plageconsult_id` = $this->_id
                AND `consultation`.`patient_id` IS NOT NULL
                AND `consultation`.`annule` = '0'";
      
    $this->_affected = intval($this->_spec->ds->loadResult($query));

    if ($this->_total) {
      $this->_fill_rate = round($this->_affected/$this->_total*100);
    }
  }
  
  function getUtilisation() {
    $this->loadRefsConsultations(false);
    $i = $this->debut;
    for($i = $this->debut; $i < $this->fin; $i = mbAddTime("+".$this->freq, $i)) {
      $utilisation[$i] = 0;
    }
    foreach($this->_ref_consultations as $_consult) {
      if(!isset($utilisation[$_consult->heure])) {
        continue;
      }
      $emplacement = $_consult->heure;
      for($i = 0; $i < $_consult->duree; $i++) {
        if(isset($utilisation[$emplacement])) {
          $utilisation[$emplacement]++;
        }
        $emplacement = mbAddTime("+".$this->freq, $emplacement);
      }
    }
    return $utilisation;
  }
  
  function loadCategorieFill() {
    if (!$this->_id) {
      return;
    }
  	$query = "SELECT `consultation`.`categorie_id`, COUNT(`consultation`.`categorie_id`) as nb,
  	                 `consultation_cat`.`nom_icone`, `consultation_cat`.`nom_categorie`
              FROM `consultation`
              LEFT JOIN `consultation_cat`
                ON `consultation`.`categorie_id` = `consultation_cat`.`categorie_id`
              WHERE `consultation`.`plageconsult_id` = $this->_id
                AND `consultation`.`annule` = '0'
                AND `consultation`.`categorie_id` IS NOT NULL
              GROUP BY `consultation`.`categorie_id`
              ORDER BY `consultation`.`categorie_id`";
  	$this->_consult_by_categorie = $this->_spec->ds->loadList($query);
  }
  
  function loadRefs($withCanceled = true, $cache = 0) {
    $this->loadRefsFwd($cache);
    $this->loadRefsBack($withCanceled);
  }
  
  function loadRefsFwd($cache = 0) {
    $this->_ref_chir = $this->loadFwdRef("chir_id");
    $this->_ref_remplacant = $this->loadFwdRef("remplacant_id");
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir) {
      $this->loadRefsFwd(1);
    }
		
    return $this->_ref_chir->getPerm($permType) 
		  && $this->_ref_module->getPerm($permType);
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
    $this->_colliding_plages = array();
    
    $this->completeField("debut");
    $this->completeField("fin");
		
		$this->_fin = $this->fin == "00:00:00" ? "23:59:59" : $this->fin;
		foreach ($plages as $plage) {
			$plage->_fin = $plage->fin == "00:00:00" ? "23:59:59" : $plage->fin;
			if (($plage->debut <  $this->_fin   and $plage->_fin >  $this->_fin  )
        or($plage->debut <  $this->debut and $plage->_fin >  $this->debut)
        or($plage->debut >= $this->debut and $plage->_fin <= $this->_fin  )) {
        $msg .= "Collision avec la plage du $this->date, de $plage->debut à $plage->_fin.";
        $this->_colliding_plages[$plage->_id] = $plage;
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
    $this->updatePlainFields();
    
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
    $this->_view = sprintf(
      "Plage du %s de %s à %s",
      mbTransformTime($this->date , null, CAppUI::conf("date")),
      mbTransformTime($this->debut, null, CAppUI::conf("time")),
      mbTransformTime($this->fin  , null, CAppUI::conf("time"))
    );

    $this->_total = mbTimeCountIntervals($this->debut, $this->fin, $this->freq);
    $this->_freq  = substr($this->freq, 3, 2);
  }
  
  function updatePlainFields() {
    if ($this->_freq !== null) {
      $this->freq  = "00:". $this->_freq. ":00";
    }
  
    // @todo: Still useful? Not so sure...
	if ($this->fin && $this->fin == "00:00:00"){
	  	$this->fin = "23:59:59";
	  }
	}
  
  function becomeNext() {
    $week_jumped = 0;
    switch($this->_type_repeat) {
      case "quadruple": 
        $this->date = mbDate("+1 WEEK", $this->date); // 4
        $week_jumped++;
      case "triple": 
        $this->date = mbDate("+1 WEEK", $this->date); // 3
        $week_jumped++;
      case "double": 
        $this->date = mbDate("+1 WEEK", $this->date); // 2
        $week_jumped++;
      case "simple": 
        $this->date = mbDate("+1 WEEK", $this->date); // 1
        $week_jumped++;
        break;
      case "sameweek":
        $week_number = CMbDate::weekNumberInMonth($this->date);
        $next_month  = CMbDate::monthNumber(mbDate("+1 MONTH", $this->date));
        $i=0;
        do {
          $this->date = mbDate("+1 WEEK", $this->date);
          $week_jumped++;
          $i++;
        } while(
          $i<10 && 
          (CMbDate::monthNumber($this->date)       <  $next_month) ||
          (CMbDate::weekNumberInMonth($this->date) != $week_number)
        );
      break;
    }
    
    // Stockage des champs modifiés
    $debut   = $this->debut;
    $fin     = $this->fin;
    $freq    = $this->freq;
    $libelle = $this->libelle;
    $locked  = $this->locked;

    // Recherche de la plage suivante
    $where["date"]    = "= '$this->date'";
    $where["chir_id"] = "= '$this->chir_id'";
    $where[]          = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    if (!$this->loadObject($where)) {
      $this->plageconsult_id = null;
    }

    // Remise en place des champs modifiés
    $this->debut   = $debut;
    $this->fin     = $fin;
    $this->freq    = $freq;
    $this->libelle = $libelle;
    $this->locked  = $locked;
    $this->updateFormFields();
    return $week_jumped;
  }    
}

$pcConfig = CAppUI::conf("dPcabinet CPlageconsult");

CPlageconsult::$hours_start = str_pad($pcConfig["hours_start"],2,"0",STR_PAD_LEFT);
CPlageconsult::$hours_stop  = str_pad($pcConfig["hours_stop" ],2,"0",STR_PAD_LEFT);
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