<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlageOp extends CMbObject {
  const RANK_VALIDATE = 1;
  const RANK_REORDER  = 2;
  
  static $minutes = array();
  static $hours = array();
  static $hours_start = null;
  static $hours_stop = null;
  static $minutes_interval = null;
  
  // DB Table key
  public $plageop_id;
  
  // DB References
  public $chir_id;
  public $anesth_id;
  public $spec_id;
  public $salle_id;
  public $spec_repl_id;
  public $secondary_function_id;

  // DB fields
  public $date;
  public $debut;
  public $fin;
  public $unique_chir;
  public $temps_inter_op;
  public $max_intervention;
  public $verrouillage;
  public $delay_repl;
  public $actes_locked;
    
  // Form Fields
  public $_day;
  public $_month;
  public $_year;
  public $_duree_prevue;
  public $_type_repeat;
  public $_nb_operations;
  public $_nb_operations_placees;
  public $_fill_rate;
  public $_reorder_up_to_interv_id;
  public $_nbQuartHeure;
  
  // Behaviour Fields
  public $_verrouillee = array();
  
  /** @var CMediusers */
  public $_ref_chir;

  /** @var CMediusers */
  public $_ref_anesth;

  /** @var CFunctions */
  public $_ref_spec;

  /** @var CFunctions */
  public $_ref_spec_repl;

  /** @var CSalle */
  public $_ref_salle;

  /** @var COperation[] */
  public $_ref_operations;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plagesop';
    $spec->key   = 'plageop_id';
    $spec->xor["owner"] = array("spec_id", "chir_id");
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"] = "COperation plageop_id";
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["chir_id"]          = "ref class|CMediusers";
    $props["anesth_id"]        = "ref class|CMediusers";
    $props["spec_id"]          = "ref class|CFunctions";
    $props["salle_id"]         = "ref notNull class|CSalle";
    $props["spec_repl_id"]     = "ref class|CFunctions";
    $props["secondary_function_id"] = "ref class|CFunctions";
    $props["date"]             = "date notNull";
    $props["debut"]            = "time notNull";
    $props["fin"]              = "time notNull moreThan|debut";
    $props["unique_chir"]      = "bool default|1";
    $props["temps_inter_op"]   = "time";
    $props["max_intervention"] = "num min|0";
    $props["verrouillage"]     = "enum list|defaut|non|oui default|defaut";
    $props["delay_repl"]       = "num min|0";
    $props["actes_locked"]     = "bool";
    
    $props["_type_repeat"]     = "enum list|simple|double|triple|quadruple|sameweek";
    return $props;
  }
  
  function loadRefs($annulee = true) {
    $this->loadRefsFwd();
    $this->loadRefsBack($annulee);
  }

  /**
   * @param bool $cache
   *
   * @return CMediusers
   */
  function loadRefChir($cache = true) {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CMediusers
   */
  function loadRefAnesth($cache = true) {
    return $this->_ref_anesth = $this->loadFwdRef("anesth_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CFunctions
   */
  function loadRefSpec($cache = true) {
    return $this->_ref_spec = $this->loadFwdRef("spec_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CFunctions
   */
  function loadRefSpecRepl($cache = true) {
    return $this->_ref_spec_repl = $this->loadFwdRef("spec_repl_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CSalle
   */
  function loadRefSalle($cache = true) {
    return $this->_ref_salle = $this->loadFwdRef("salle_id", $cache);
  }
  
  function makeView(){
    if ($this->spec_id) {
      $this->_view = $this->_ref_spec->_shortview;
    }
    
    if ($this->chir_id) {
      $this->_view = $this->_ref_chir->_view;
    }

    if ($this->anesth_id) {
      $this->_view .= " - ".$this->_ref_anesth->_shortview;
    }  
  }

  function loadRefsFwd($cache = false) {
    $this->loadRefChir($cache);
    $this->loadRefAnesth($cache);
    $this->loadRefSpec($cache);
    $this->loadRefSalle($cache);
    $this->makeView();
  }
  
  function loadRefsOperations($annulee = true, $order = "rank, rank_voulu, horaire_voulu", $sorted = false, $validated = null, $where = array()) {
    $where += array(
      "plageop_id" => "= '$this->plageop_id'",
    );
    
    if (!$annulee) {
      $where["annulee"] = "= '0'";
    }

    /** @var COperation[] $intervs */
    $intervs = array();

    $op = new COperation;
    
    if (!$sorted) {
      $intervs = $op->loadList($where, $order);
    }
    else {
      $order = "rank, rank_voulu, horaire_voulu";
      
      if ($validated === null || $validated === true) {
        $where["rank"] = "> 0";
        $intervs = CMbArray::mergeKeys($intervs, $op->loadList($where, $order));
      }
      
      if ($validated === null || $validated === false) {
        // Sans rank
        $where["rank"] = "= 0";
        
        $where["rank_voulu"] = "> 0";
        $intervs = CMbArray::mergeKeys($intervs, $op->loadList($where, $order));
          
        // Sans rank voulu
        $where["rank_voulu"] = "= 0";
        
        $where["horaire_voulu"] = "IS NOT NULL";
        $intervs = CMbArray::mergeKeys($intervs, $op->loadList($where, $order));
        
        $where["horaire_voulu"] = "IS NULL";
        $intervs = CMbArray::mergeKeys($intervs, $op->loadList($where, $order));
      }
    }
    
    foreach ($intervs as $operation) {
      $operation->_ref_plageop = $this;
    }
    
    return $this->_ref_operations = $intervs;
  }

  function loadRefsBack($annulee = true, $order = "rank, rank_voulu, horaire_voulu") {
    $this->loadRefsOperations($annulee, $order);
  }
  
  /** 
   * Mise à jour des horaires en fonction de l'ordre des operations, 
   * et mise a jour des rank, de sorte qu'ils soient consecutifs
   */
  function reorderOp($action = null) {
    $this->completeField("debut", "temps_inter_op");
    
    if (!count($this->_ref_operations)) {
      $with_cancelled = CAppUI::conf("dPplanningOp COperation save_rank_annulee_validee");
      $this->loadRefsOperations($with_cancelled, "rank, rank_voulu, horaire_voulu", true);
    }
    
    $new_time = $this->debut;
    $plage_multipraticien = $this->spec_id && !$this->unique_chir;
    
    $prev_op = new COperation();
    $i = 0;
    foreach ($this->_ref_operations as $op) {
      // Intervention deja validée ou si on veut valider
      if ($op->rank || ($action & self::RANK_VALIDATE)) {
        $op->rank = ++$i;
        
        // Creation des pauses si plage multi-praticien
        if ($plage_multipraticien && ($action & self::RANK_VALIDATE)) {
          if ($prev_op->_id) {
            $op->time_operation = max($new_time, $op->horaire_voulu);
            
            $prev_op->_pause_hour = $prev_op->_pause_min = null; // FIXME ARHHHH
            $prev_op->pause = CMbDT::subTime($new_time, $op->time_operation);
            $prev_op->store(false);
          }
          else {
            $op->time_operation = $new_time;
          }
          
          $prev_op = $op;
        }
        else {
          $op->time_operation = $new_time;
        }
        
        // Pour faire suivre un changement de salle
        if ($this->salle_id && $this->fieldModified("salle_id")) {
          $op->salle_id = $this->salle_id;
        }
      }
      
      // Plage monopraticien
      elseif (!$plage_multipraticien && 
              ($action & self::RANK_REORDER) && 
              ($op->horaire_voulu || $this->_reorder_up_to_interv_id)) {
        $op->rank_voulu = ++$i;
        $op->horaire_voulu = $new_time;
      }
      
      if ($this->_reorder_up_to_interv_id == $op->_id) {
        $this->_reorder_up_to_interv_id = null;
      }
      
      $op->updateFormFields();
      $op->store(false);
      
      // Durée de l'operation
      // + durée entre les operations
      // + durée de pause
      $new_time = CMbDT::addTime($op->temp_operation, $new_time);
      $new_time = CMbDT::addTime($this->temps_inter_op, $new_time);
      $new_time = CMbDT::addTime($op->pause, $new_time);
    }
    return true;
  }

  function guessHoraireVoulu() {
    if ($this->spec_id && !$this->unique_chir) {
      return false;
    }
    $this->completeField("debut", "temps_inter_op");
    
    $new_time = $this->debut;
    foreach ($this->_ref_operations as $op) {
      $op->_horaire_voulu = $new_time;
      
      // Durée de l'operation
      // + durée entre les operations
      // + durée de pause
      $new_time = CMbDT::addTime($op->temp_operation, $new_time);
      $new_time = CMbDT::addTime($this->temps_inter_op, $new_time);
      $new_time = CMbDT::addTime($op->pause, $new_time);
    }
    return true;
  }
  

  /**
   * returns collision message, null for no collision
   *
   * @return string
   */
  function hasCollisions() {
    $this->completeField("salle_id");
    $this->completeField("date");
    
    // Get all other plages the same day
    $where = array();
    $where["salle_id"]   = "= '$this->salle_id'";
    $where["date"]       = "= '$this->date'";
    $where["plageop_id"] = "!= '$this->plageop_id'";
    $plages = $this->loadList($where);
    $msg = null;
    foreach ($plages as $plage) {
      if (($plage->debut < $this->fin and $plage->fin > $this->fin)
        or($plage->debut < $this->debut and $plage->fin > $this->debut)
        or($plage->debut >= $this->debut and $plage->fin <= $this->fin)) {
        $msg .= "Collision avec la plage du $plage->date, de $plage->debut à $plage->fin. ";
      }
    }

    return $msg;   
  }

  function check() {
    // Data checking
    $msg = null;
    if (!$this->plageop_id && !$this->chir_id && !$this->spec_id) {
      $msg .= "Vous devez choisir un praticien ou une spécialité<br />";
    }
    return $msg . parent::check();
  }
  
  function store() {
    $this->updatePlainFields();
    if ($msg = $this->hasCollisions()) {
      return $msg;
    }
    $oldPlage = new CPlageOp;
    if ($this->_id) {
      $oldPlage->load($this->_id);
      $oldPlage->loadRefsBack();
    }
    // Erreur si on est en multi-praticiens, qu'il y a des interventions et qu'on veut mettre un praticien
    if (null !== $this->chir_id && $this->_id && !$this->unique_chir) {
      if (count($oldPlage->_ref_operations) && $oldPlage->spec_id && $this->chir_id) {
        $msg = "Impossible de selectionner un praticien : ".count($oldPlage->_ref_operations)." intervention(s) déjà présentes dans une plage multi-praticiens";
        return $msg;
      }
    }
    
    // Erreur si on change de praticien alors qu'il y a déjà des interventions
    if (null !== $this->chir_id && $this->_id) {
      if (count($oldPlage->_ref_operations) && $oldPlage->chir_id && ($oldPlage->chir_id != $this->chir_id)) {
        $msg = "Impossible de changer le praticien : ".count($oldPlage->_ref_operations)." intervention(s) déjà présentes";
        return $msg;
      }
    }
    
    // Erreur si on créé / modifier une plage sur une salle bloquée
    $salle = $this->loadRefSalle();
    if (count($salle->loadRefsBlocages($this->date))) {
      $msg = "Impossible de " . ($this->_id ? "modifier" : "créer") . " la plage : la salle $salle est bloquée";
      return $msg;
    }
    
    // Modification du salle_id de la plage -> repercussion sur les interventions
    if ($this->_id && $this->salle_id && $this->salle_id != $oldPlage->salle_id) {
      foreach ($oldPlage->_ref_operations as &$_operation) {
        if ($_operation->salle_id == $oldPlage->salle_id) {
          $_operation->salle_id = $this->salle_id;
          $_operation->store(false);
        }
      }
    }

    // Modification du début de la plage ou des minutes entre les interventions
    $this->completeField("debut", "temps_inter_op");

    if ($this->_id && ($this->debut != $oldPlage->debut || $this->temps_inter_op != $oldPlage->temps_inter_op)) {
      $this->reorderOp();
    }

    return parent::store();
  }

  function delete() {
    $this->completeField("salle_id", "date");
    $this->loadRefsOperations();

    foreach ($this->_ref_operations as $_op) {
      if ($_op->annulee) {
        $_op->plageop_id = "";
        $_op->date       = $this->date;
        $_op->salle_id   = $this->salle_id;
        $_op->store();
      }
    }

    return parent::delete();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_duree_prevue = CMbDT::timeRelative($this->debut, $this->fin);
    $this->_view = "Plage du ".$this->getFormattedValue("date");
  }  
  
  /**
   * find the next plageop according
   * to the current plageop parameters
   * return the number of weeks jumped
   *
   * @return int
   */
  function becomeNext($init_salle_id = null, $init_chir_id = null, $init_spec_id = null, $init_secondary_function_id = null) {
    $week_jumped = 0;
    if (!$this->_type_repeat) {
      $this->_type_repeat = "simple";
    }

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
    }
    
    // Stockage des champs modifiés
    $salle_id         = $this->salle_id;
    $chir_id          = $this->chir_id;
    $spec_id          = $this->spec_id;
    $secondary_function_id = $this->secondary_function_id === null ? "" : $this->secondary_function_id;
    $debut            = $this->debut;
    $fin              = $this->fin;
    $temps_inter_op   = $this->temps_inter_op;
    $max_intervention = $this->max_intervention;
    $anesth_id        = $this->anesth_id;
    $delay_repl       = $this->delay_repl;
    $spec_repl_id     = $this->spec_repl_id;
    $type_repeat      = $this->_type_repeat;
    $unique_chir      = $this->unique_chir;

    // Recherche de la plage suivante
    $where             = array();
    $where["date"]     = "= '$this->date'";
    $where[]           = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    $where["salle_id"] = $init_salle_id ? "= '$init_salle_id'" : "= '$this->salle_id'";
    if ($chir_id || $init_chir_id) {
      $where["chir_id"] = $init_chir_id ? "= '$init_chir_id'" : "= '$chir_id'";
    }
    else {
      $where["spec_id"] = $init_spec_id ? "= '$init_spec_id'" : "= '$spec_id'";
    }
    if ($secondary_function_id || $init_secondary_function_id) {
      $where["secondary_function_id"] = $init_secondary_function_id ? "= '$init_secondary_function_id'" : "= '$secondary_function_id'";
    }

    $plages           = $this->loadList($where);
    if (count($plages) > 0) {
      $this->load(reset($plages)->plageop_id);
    }
    else {
      $this->plageop_id = null;
    }
    if (!$this->chir_id) {
      $this->chir_id = "";
    }
    if (!$this->spec_id) {
      $this->spec_id = "";
    }

    // Remise en place des champs modifiés
    $this->salle_id         = $salle_id;
    $this->chir_id          = $chir_id;
    $this->secondary_function_id = $secondary_function_id;
    $this->spec_id          = $spec_id;
    $this->debut            = $debut;
    $this->fin              = $fin;
    $this->temps_inter_op   = $temps_inter_op;
    $this->max_intervention = $max_intervention;
    $this->anesth_id        = $anesth_id;
    $this->delay_repl       = $delay_repl;
    $this->spec_repl_id     = $spec_repl_id;
    $this->_type_repeat     = $type_repeat;
    $this->unique_chir      = $unique_chir;
    $this->updateFormFields();
    return $week_jumped;
  }

  function getNbOperations($addedTime = null, $useTimeInterOp = true) {
    if ($useTimeInterOp == true) {
      $select_time = "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`) + TIME_TO_SEC(`plagesop`.`temps_inter_op`)) AS time";
    }
    else {
      $select_time = "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`)) AS time";
    }
        
    $sql = "SELECT COUNT(`operations`.`operation_id`) AS total, $select_time
        FROM `operations`, `plagesop`
        WHERE `operations`.`plageop_id` = '$this->plageop_id'
        AND `operations`.`plageop_id` = `plagesop`.`plageop_id`
        AND `operations`.`annulee` = '0'";
    $result = $this->_spec->ds->loadHash($sql);
    $this->_nb_operations = $result["total"];
    if ($addedTime) {
      $result["time"] = $result["time"] + $addedTime;
    }
    $this->_fill_rate = number_format($result["time"]*100/(strtotime($this->fin)-strtotime($this->debut)), 2);
        
    $sql = "SELECT COUNT(`operations`.`operation_id`) AS total, $select_time
        FROM `operations`, `plagesop`
        WHERE `operations`.`plageop_id` = '$this->plageop_id'
        AND `operations`.`plageop_id` = `plagesop`.`plageop_id`
        AND `operations`.`rank` > 0
        AND `operations`.`annulee` = '0'";
    $result = $this->_spec->ds->loadHash($sql);
    $this->_nb_operations_placees = $result["total"];
    
    if ($this->verrouillage == "oui") {
      $this->_verrouillee = array("force");
    }
    elseif ($this->verrouillage == "non") {
      $this->_verrouillee = array();
    }
    else {
      $this->loadRefSalle();
      $this->_ref_salle->loadRefBloc();
      $date_min = CMbDT::date("+ " . $this->_ref_salle->_ref_bloc->days_locked . " DAYS");
      $check_datemin = $this->date < $date_min;
      $check_fill    = ($this->_fill_rate > 100) && CAppUI::conf("dPbloc CPlageOp locked");
      $check_max     = $this->max_intervention && $this->_nb_operations >= $this->max_intervention;

      if ($check_datemin) {
        $this->_verrouillee[] = "datemin";
      }
      if ($check_fill) {
        $this->_verrouillee[] = "fill";
      }
      if ($check_max) {
        $this->_verrouillee[] = "max";
      }
    }
    
  }
  
  function getPerm($permType) {
    if (!$this->_ref_salle) {
      $this->loadRefSalle();
    }
    if ($this->chir_id && !$this->_ref_chir) {
      $this->loadRefChir();
    }
    if ($this->spec_id && !$this->_ref_spec) {
      $this->loadRefSpec();
    }

    $pratPerm = false;

    // Test de Permission
    if ($this->chir_id) {
      $pratPerm = $this->_ref_chir->getPerm($permType);
    }
    elseif ($this->spec_id) {
      $pratPerm = $this->_ref_spec->getPerm($permType);
    }
    
    return ($this->_ref_salle->getPerm($permType) && $pratPerm);
  }
}

$pcConfig = CAppUI::conf("dPbloc CPlageOp");

CPlageOp::$hours_start = str_pad(CValue::first($pcConfig["hours_start"], "08"), 2, "0", STR_PAD_LEFT);
CPlageOp::$hours_stop  = str_pad(CValue::first($pcConfig["hours_stop"], "20"), 2, "0", STR_PAD_LEFT);
CPlageOp::$minutes_interval = CValue::first($pcConfig["minutes_interval"], "15");

$listHours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
$listMins  = range(0, 59, CPlageOp::$minutes_interval);

foreach ($listHours as $key => $hour) {
  CPlageOp::$hours[$hour] = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

foreach ($listMins as $key => $min) {
  CPlageOp::$minutes[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}
