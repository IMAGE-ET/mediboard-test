<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CPlageconsult extends CPlageHoraire {
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
  var $pour_compte_id = null;

  // DB fields
  var $freq    = null;
  var $libelle = null;
  var $locked  = null;
  var $remplacant_ok = null;
  var $desistee = null;
  var $color = null;
  var $pct_retrocession = null;

  // Form fields
  var $_freq                 = null;
  var $_affected             = null;
  var $_total                = null;
  var $_fill_rate            = null;
  var $_nb_patients          = null;
  var $_consult_by_categorie = null;
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
  var $_ref_pour_compte    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table          = "plageconsult";
    $spec->key            = "plageconsult_id";
    $spec->collision_keys = array("chir_id");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation plageconsult_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();

    $props["chir_id"]          = "ref notNull class|CMediusers seekable";
    $props["remplacant_id"]    = "ref class|CMediusers seekable";
    $props["pour_compte_id"]   = "ref class|CMediusers seekable";
    $props["date"]             = "date notNull";
    $props["freq"]             = "time notNull";
    $props["debut"]            = "time notNull";
    $props["fin"]              = "time notNull moreThan|debut";
    $props["libelle"]          = "str seekable";
    $props["locked"]           = "bool default|0";
    $props["remplacant_ok"]    = "bool default|0 show|0";
    $props["desistee"]         = "bool default|0 show|0";
    $props["color"]            = "str length|6 default|DDDDDD";
    $props["pct_retrocession"] = "pct default|70 show|0";

    // Form fields
    $props["_freq"]        = "";
    $props["_affected"]    = "";
    $props["_total"]       = "";
    $props["_fill_rate"]   = "";
    $props["_type_repeat"] = "enum list|simple|double|triple|quadruple|sameweek";

    // Filter fields
    $props["_date_min"]          = "date";
    $props["_date_max"]          = "date moreThan|_date_min";
    $props["_function_id"]       = "ref class|CFunctions";
    $props["_other_function_id"] = "ref class|CFunctions";
    $props["_user_id"]           = "ref class|CMediusers";

    return $props;
  }

  /**
   * Load consultations
   *
   * @param bool $withCanceled Include cancelled consults
   * @param bool $withClosed   Include closed consults
   * @param bool $withPayees   Include payed consults
   *
   * @return CConsultation[]
   */
  function loadRefsConsultations($withCanceled = true, $withClosed = true, $withPayees = true) {
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
  
    if (!$withPayees) {
      foreach ($this->_ref_consultations as $key => $consult) {
        $facture = $consult->loadRefFacture();
        if ($facture->_id && $facture->patient_date_reglement) {
          unset($this->_ref_consultations[$key]);
        }
      }
    }
    return $this->_ref_consultations;
  }

  /**
   * @return int The patient count
   */
  function countPatients(){
    $consultation = new CConsultation();
    $consultation->plageconsult_id = $this->_id;
    $where["plageconsult_id"] = "= '$this->_id'";
    $where["patient_id"] = " IS NOT NULL";
    $where["annule"] = "= '0'";
    return $this->_nb_patients = $consultation->countList($where);
  }

  function loadRefsBack($withCanceled = true, $withClosed = true, $withPayees = true) {
    $this->loadRefsConsultations($withCanceled, $withClosed, $withPayees);
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

    for ($i = $this->debut; $i < $this->fin; $i = CMbDT::addTime("+".$this->freq, $i)) {
      $utilisation[$i] = 0;
    }

    foreach ($this->_ref_consultations as $_consult) {
      if (!isset($utilisation[$_consult->heure])) {
        continue;
      }
      $emplacement = $_consult->heure;
      for ($i = 0; $i < $_consult->duree; $i++) {
        if (isset($utilisation[$emplacement])) {
          $utilisation[$emplacement]++;
        }
        $emplacement = CMbDT::addTime("+".$this->freq, $emplacement);
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
    $this->_ref_chir        = $this->loadFwdRef("chir_id"       , $cache);
    $this->_ref_remplacant  = $this->loadFwdRef("remplacant_id" , $cache);
    $this->_ref_pour_compte = $this->loadFwdRef("pour_compte_id", $cache);
  }

  function loadRefChir() {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", true);
  }
  
  function getPerm($permType) {
    if (!$this->_ref_chir) {
      $this->loadRefsFwd(1);
    }

    return $this->_ref_chir->getPerm($permType) 
      && $this->_ref_module->getPerm($permType);
  }

  function check() {
    // Data checking
    $msg = null;

    if (!$this->plageconsult_id) {
      if (!$this->chir_id) {
        $msg .= "Praticien non valide<br />";
      }
    }

    //chir_id se remplace lui même
    if ($this->chir_id == $this->pour_compte_id) {
      $msg .= "Vous ne pouvez vous remplacer vous même";
    }

    return $msg . parent::check();
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_total = CMbDT::timeCountIntervals($this->debut, $this->fin, $this->freq);

    if ($this->freq == "1:00:00" || $this->freq == "01:00:00") {
      $this->_freq = "60";
    }
    else {
      $this->_freq = substr($this->freq, 3, 2);
    }
  }

  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->_freq !== null) {
      if ($this->_freq == "60") {
        $this->freq = "01:00:00";
      }
      else {
        $this->freq = sprintf("00:%02d:00", $this->_freq);
      }
    }
  }

  function becomeNext() {
    $week_jumped = 0;

    switch ($this->_type_repeat) {
      case "quadruple": 
        $this->date = CMbDT::date("+1 WEEK", $this->date); // 4
        $week_jumped++;
      case "triple": 
        $this->date = CMbDT::date("+1 WEEK", $this->date); // 3
        $week_jumped++;
      case "double": 
        $this->date = CMbDT::date("+1 WEEK", $this->date); // 2
        $week_jumped++;
      case "simple": 
        $this->date = CMbDT::date("+1 WEEK", $this->date); // 1
        $week_jumped++;
        break;
      case "sameweek":
        $week_number = CMbDate::weekNumberInMonth($this->date);
        $next_month  = CMbDate::monthNumber(CMbDT::date("+1 MONTH", $this->date));
        $i = 0;
        do {
          $this->date = CMbDT::date("+1 WEEK", $this->date);
          $week_jumped++;
          $i++;
        } while (
          $i < 10 &&
          (CMbDate::monthNumber($this->date)       <  $next_month) ||
          (CMbDate::weekNumberInMonth($this->date) != $week_number)
        );
        break;
      default:
        return ++$week_jumped;
    }

    // Stockage des champs modifiés
    $debut   = $this->debut;
    $fin     = $this->fin;
    $freq    = $this->freq;
    $libelle = $this->libelle;
    $locked  = $this->locked;
    $color   = $this->color;
    $desistee       = $this->desistee;
    $remplacant_id  = $this->remplacant_id;
    $pour_compte_id  = $this->pour_compte_id;

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
    $this->color   = $color;
    $this->desistee        = $desistee;
    $this->remplacant_id   = $remplacant_id;
    $this->pour_compte_id  = $pour_compte_id;
    $this->updateFormFields();
    return $week_jumped;
  }

  function store() {
    $this->completeField("pour_compte_id", "chir_id");
    $change_pour_compte = $this->fieldModified("pour_compte_id");
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($change_pour_compte) {
      $consults = $this->loadRefsConsultations();

      foreach ($consults as $_consult) {
        $facture = $_consult->loadRefFacture();
        $facture->praticien_id = ($this->pour_compte_id ? $this->pour_compte_id : $this->chir_id);
        $facture->store();
      }
    }
  }
}

$pcConfig = CAppUI::conf("dPcabinet CPlageconsult");

CPlageconsult::$hours_start = str_pad($pcConfig["hours_start"], 2, "0", STR_PAD_LEFT);
CPlageconsult::$hours_stop  = str_pad($pcConfig["hours_stop" ], 2, "0", STR_PAD_LEFT);
CPlageconsult::$minutes_interval = CValue::first($pcConfig["minutes_interval"], "15");

$hours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
$mins  = range(0, 59, CPlageconsult::$minutes_interval);

foreach ($hours as $key => $hour) {
  CPlageconsult::$hours[$hour] = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

foreach ($mins as $key => $min) {
  CPlageconsult::$minutes[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}
