<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Plages de consultation médicales et para-médicales
 */

class CPlageconsult extends CPlageHoraire {
  static $minutes = array();
  static $hours = array();
  static $hours_start = null;
  static $hours_stop = null;
  static $minutes_interval = null;

  // DB Table key
  public $plageconsult_id;

  // DB References
  public $chir_id;
  public $remplacant_id;
  public $pour_compte_id;

  // DB fields
  public $freq;
  public $libelle;
  public $locked;
  public $remplacant_ok;
  public $desistee;
  public $color;
  public $pct_retrocession;
  public $pour_tiers;

  // Form fields
  public $_freq;
  public $_affected;
  public $_total;
  public $_fill_rate;
  public $_nb_patients;
  public $_consult_by_categorie;
  public $_type_repeat;
  public $_propagation;
  public $_nb_free_freq;

  // Filter fields
  public $_date_min;
  public $_date_max;
  public $_function_id;
  public $_other_function_id;
  public $_user_id;

  // behaviour fields
  public $_handler_external_booking;
  public $_immediate_plage;

  /** @var CMediusers */
  public $_ref_chir;

  /** @var CConsultation[] */
  public $_ref_consultations;

  /** @var CMediusers */
  public $_ref_remplacant;

  /** @var CMediusers */
  public $_ref_pour_compte;

  public $_disponibilities;

  public $_freq_minutes;          // freq in minutes (int)
  public $_cumulative_minutes = 0;    // nb minutes usef for consultation in this plage

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table          = "plageconsult";
    $spec->key            = "plageconsult_id";
    $spec->collision_keys = array("chir_id");
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation plageconsult_id";
    $backProps["identifiants"] = "CIdSante400 object_id cascade";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["chir_id"]          = "ref notNull class|CMediusers seekable";
    $props["remplacant_id"]    = "ref class|CMediusers seekable";
    $props["pour_compte_id"]   = "ref class|CMediusers seekable";
    $props["date"]             = "date notNull";
    $props["freq"]             = "time notNull min|00:05:00";
    $props["debut"]            = "time notNull";
    $props["fin"]              = "time notNull moreThan|debut";
    $props["libelle"]          = "str seekable";
    $props["locked"]           = "bool default|0";
    $props["remplacant_ok"]    = "bool default|0 show|0";
    $props["desistee"]         = "bool default|0 show|0";
    $props["color"]            = "color default|dddddd";
    $props["pct_retrocession"] = "pct default|70 show|0";
    $props["pour_tiers"]       = "bool default|0 show|0";

    // Form fields
    $props["_freq"]        = "";
    $props["_affected"]    = "";
    $props["_total"]       = "";
    $props["_fill_rate"]   = "";
    $props["_type_repeat"] = "enum list|simple|double|triple|quadruple|quintuple|sextuple|septuple|octuple|sameweek";
    $props["_propagation"] = "bool default|0";

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


    foreach ($this->_ref_consultations as $_consult) {
      $this->_cumulative_minutes += ($_consult->duree * $this->_freq_minutes);
    }


    if (!$withPayees) {
      foreach ($this->_ref_consultations as $key => $consult) {
        /** @var CConsultation $consult */
        $facture = $consult->loadRefFacture();
        if ($facture->_id && $facture->patient_date_reglement) {
          unset($this->_ref_consultations[$key]);
        }
      }
    }
    return $this->_ref_consultations;
  }

  /**
   * get the next plage for the chir_id
   *
   * @return CPlageconsult
   */
  function getNextPlage() {
    $plage = new CPlageconsult();
    if (!$this->_id) {
      return $plage;
    }

    $where = array();
    $where[] = " chir_id = '$this->chir_id' OR remplacant_id = '$this->remplacant_id'";
    $where["locked"] = " != '1' ";
    $where["date"] = "> '$this->date' ";
    $where["plageconsult_id"] = " != '$this->plageconsult_id' ";
    $plage->loadObject($where, "date ASC, debut ASC");
    return $plage;
  }

  function getPreviousPlage() {
    $plage = new CPlageconsult();
    if (!$this->_id) {
      return $plage;
    }

    $where = array();
    $where[] = " chir_id = '$this->chir_id' OR remplacant_id = '$this->remplacant_id'";
    $where["locked"] = " != '1' ";
    $where["date"] = "< '$this->date' ";
    $where["plageconsult_id"] = " != '$this->plageconsult_id' ";
    $plage->loadObject($where, "date DESC, debut DESC");
    return $plage;
  }

  /**
   * get the plage list between 2 days or for one day
   *
   * @param string      $chir_id    chir of plage
   * @param string      $date_start date of start
   * @param string|null $date_end   date of end (if null, check only for start)
   *
   * @return CPlageconsult[]
   */
  function loadForDays($chir_id, $date_start, $date_end = null) {
    $plage = new self();
    $where = array();
    $chir = new CMediusers();
    $chir->load($chir_id);
    $where["date"] = $date_end ? ("BETWEEN '$date_start' AND '$date_end' ") : " = '$date_start'";
    $where[] = " chir_id = '$chir_id' OR remplacant_id = '$chir_id'";
    return $plage->loadList($where);
  }

  /**
   * Calcul du nombre de patient dans la plage
   *
   * @param bool $include_pause count pauses too
   *
   * @return int The patient count
   */
  function countPatients($include_pause = false){
    $consultation = new CConsultation();
    $consultation->plageconsult_id = $this->_id;
    $where["plageconsult_id"] = "= '$this->_id'";
    if (!$include_pause) {
      $where["patient_id"] = " IS NOT NULL";
    }
    $where["annule"] = "= '0'";
    return $this->_nb_patients = $consultation->countList($where);
  }

  /**
   * Refs consultations and fill rate loader
   *
   * @param bool $withCanceled Prise en compte des consultations annulées
   * @param bool $withClosed   Prise en compte des consultations terminées
   * @param bool $withPayees   Prise en compte des consultations payées
   *
   * @return void
   */
  function loadRefsBack($withCanceled = true, $withClosed = true, $withPayees = true) {
    $this->loadRefsConsultations($withCanceled, $withClosed, $withPayees);
    $this->loadFillRate();
  }

  /**
   *
   */
  function loadDisponibilities()  {
    $fill = array();
    $time = $this->debut;
    $nb_plage_prise = 0;
    $nb_place_consult = round((CMbDT::minutesRelative($this->debut, $this->fin)/$this->_freq));

    for ($a=0; $a < $nb_place_consult; $a++) {
      if (!isset($fill[$time])) {
        $fill[$time] = 0;
      }

      //there is something ...
      foreach ($this->_ref_consultations as $_consult) {
        if ($_consult->heure == $time) {
          $status = 0;

          // pause
          if (!$_consult->patient_id) {
            $status = -1;
          }
          else {
            if (!$_consult->annule) {
              $status = 1;
            }
          }
          // repetition
          $temp_time = $time;
          for ($b=0; $b<$_consult->duree; $b++) {
            if ($status != 0) {
              $fill[$temp_time] = $status;
              $nb_plage_prise++;
            }

            $temp_time = CMbDT::addTime($this->freq, $temp_time);
          }
        }
      }
      $time = CMbDT::addTime($this->freq, $time);
    }

    $this->_affected = $nb_plage_prise;
    $this->_nb_free_freq = $nb_place_consult-$this->_affected;
    return $this->_disponibilities = $fill;
  }

  /**
   * Plageconsult fill rate loader
   *
   * @return void
   */
  function loadFillRate() {
    if (!$this->_id) {
      return;
    }

    $query = "SELECT SUM(`consultation`.`duree`)
              FROM `consultation`
              WHERE `consultation`.`plageconsult_id` = '$this->_id'
                AND `consultation`.`annule` = '0'";

    $this->_affected = intval($this->_spec->ds->loadResult($query));

    if ($this->_total) {
      $this->_fill_rate = round($this->_affected/$this->_total*100);
    }
  }

  /**
   * Calcul du tableau d'occupation de la plage de consultation
   *
   * @return array
   */
  function getUtilisation() {
    $this->loadRefsConsultations(false);

    $utilisation = array();
    $old = $this->debut;
    for ($i = $this->debut; $i < $this->fin; $i = CMbDT::addTime("+".$this->freq, $i)) {
      if ($old > $i) {
        break;
      }
      $utilisation[$i] = 0;
      $old = $i;
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

  /**
   * Calcul de la répartition des consultations par catégorie
   *
   * @return void
   */
  function loadCategorieFill() {
    if (!$this->_id) {
      return;
    }
    $query = "SELECT `consultation`.`categorie_id`, COUNT(`consultation`.`categorie_id`) as nb,
                     `consultation_cat`.`nom_icone`, `consultation_cat`.`nom_categorie`
              FROM `consultation`
              LEFT JOIN `consultation_cat`
                ON `consultation`.`categorie_id` = `consultation_cat`.`categorie_id`
              WHERE `consultation`.`plageconsult_id` = '$this->_id'
                AND `consultation`.`annule` = '0'
                AND `consultation`.`categorie_id` IS NOT NULL
              GROUP BY `consultation`.`categorie_id`
              ORDER BY `consultation`.`categorie_id`";
    $this->_consult_by_categorie = $this->_spec->ds->loadList($query);
  }

  /**
   * Chargement global des références
   *
   * @param bool $withCanceled Prise en compte des consultations annulées
   * @param int  $cache        Utilisation du cache
   *
   * @deprecated out of control resouce consumption
   *
   * @return void
   */
  function loadRefs($withCanceled = true, $cache = 0) {
    $this->loadRefsFwd($cache);
    $this->loadRefsBack($withCanceled);
  }

  /**
   * @see parent::loadRefsFwd()
   * @deprecated
   */
  function loadRefsFwd($cache = true) {
    $this->_ref_chir        = $this->loadFwdRef("chir_id"       , $cache);
    $this->_ref_remplacant  = $this->loadFwdRef("remplacant_id" , $cache);
    $this->_ref_pour_compte = $this->loadFwdRef("pour_compte_id", $cache);
  }

  /**
   * Chargement du praticien
   *
   * @return CMediusers
   */
  function loadRefChir() {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", true);
  }

  /**
   * Chargement du remplacant
   *
   * @return CMediusers
   */
  function loadRefRemplacant() {
    return $this->_ref_remplacant  = $this->loadFwdRef("remplacant_id" , true);
  }

  /**
   * Chargement du pour compte
   *
   * @return CMediusers
   */
  function loadRefPourCompte() {
    return $this->_ref_pour_compte  = $this->loadFwdRef("pour_compte_id", true);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_id) {
      return parent::getPerm($permType);
    }
    if (!$this->_ref_chir) {
      $this->loadRefChir();
    }
    return $this->_ref_chir->getPerm($permType) 
      && parent::getPerm($permType);
  }

  /**
   * @see parent::check()
   */
  function check() {
    // Data checking
    $msg = null;

    if (!$this->plageconsult_id) {
      if (!$this->chir_id) {
        $msg .= "Praticien non valide<br />";
      }
    }

    //plage blocked by holiday config if not immediate consultation
    if (!$this->_immediate_plage) {
      $holidays = CMbDate::getHolidays();
      if (!CAppUI::pref("allow_plage_holiday") && array_key_exists($this->date, $holidays) && !$this->_id) {
        $msg.= CAppUI::tr("CPlageConsult-errror-plage_blocked_by_holidays", $holidays[$this->date]);
      }
    }

    //chir_id se remplace lui même
    if ($this->chir_id == $this->pour_compte_id) {
      $msg .= CAppUI::tr("CPlageConsult-error-pour_compte-equal-chir_id");
    }

    if ($this->chir_id == $this->remplacant_id) {
      $msg .= CAppUI::tr("CPlageConsult-error-remplacant_id-equal-chir_id");
    }

    return $msg . parent::check();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_total = CMbDT::timeCountIntervals($this->debut, $this->fin, $this->freq);

    if ($this->freq == "1:00:00" || $this->freq == "01:00:00") {
      $this->_freq = "60";
    }
    else {
      $this->_freq = substr($this->freq, 3, 2);
      $this->_freq_minutes = CMbDT::minutesRelative("00:00:00", $this->freq);
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();
    $this->completeField("freq");
    if ($this->_freq !== null) {
      if ($this->_freq == "60") {
        $this->freq = "01:00:00";
      }
      else {
        $this->freq = sprintf("00:%02d:00", $this->_freq);
      }
    }
  }

  /**
   * Find the next occurence of similar Plageconsult
   * using the _type_repeat form field
   *
   * @return int Number of weeks jumped
   */
  function becomeNext() {
    $week_jumped = 0;

    switch ($this->_type_repeat) {
      case "octuple" :
        $this->date = CMbDT::date("+8 WEEK", $this->date); // 8
        $week_jumped += 8;
        break;
      case "septuple":
        $this->date = CMbDT::date("+7 WEEK", $this->date); // 7
        $week_jumped += 7;
        break;
      case "sextuple":
        $this->date = CMbDT::date("+6 WEEK", $this->date); // 6
        $week_jumped += 6;
        break;
      case "quintuple":
        $this->date = CMbDT::date("+5 WEEK", $this->date); // 5
        $week_jumped += 5;
        break;
      case "quadruple":
        $this->date = CMbDT::date("+4 WEEK", $this->date); // 4
        $week_jumped += 4;
        break;
      case "triple": 
        $this->date = CMbDT::date("+3 WEEK", $this->date); // 3
        $week_jumped +=3;
        break;
      case "double":
        $this->date = CMbDT::date("+2 WEEK", $this->date); // 2
        $week_jumped +=2;
        break;
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
    $debut          = $this->debut;
    $fin            = $this->fin;
    $freq           = $this->freq;
    $libelle        = $this->libelle;
    if ($this->_propagation) {
      $locked       = $this->locked;
      $pour_tiers   = $this->pour_tiers;
    }
    $color          = $this->color;
    $desistee       = $this->desistee;
    $remplacant_id  = $this->remplacant_id;
    $pour_compte_id = $this->pour_compte_id;

    // Recherche de la plage suivante
    $where["date"]    = "= '$this->date'";
    $where["chir_id"] = "= '$this->chir_id'";
    $where[]          = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    if (!$this->loadObject($where)) {
      $this->plageconsult_id = null;
    }

    // Remise en place des champs modifiés
    $this->debut          = $debut;
    $this->fin            = $fin;
    $this->freq           = $freq;
    $this->libelle        = $libelle;
    if ($this->_propagation) {
      $this->locked       = $locked;
      $this->pour_tiers   = $pour_tiers;
    }
    $this->color          = $color;
    $this->desistee       = $desistee;
    $this->remplacant_id  = $remplacant_id;
    $this->pour_compte_id = $pour_compte_id;
    $this->updateFormFields();
    return $week_jumped;
  }

  /**
   * @see parent::store()
   */
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
    return null;
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
