<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlageOp extends CMbObject {
  static $minutes = array();
  static $hours = array();
  static $hours_start = null;  
  static $hours_stop = null;
  static $minutes_interval = null;
  
  // DB Table key
  var $plageop_id = null;
  
  // DB References
  var $chir_id      = null;
  var $anesth_id    = null;
  var $spec_id      = null;
  var $salle_id     = null;
  var $spec_repl_id = null;

  // DB fields
  var $date             = null;
  var $debut            = null;
  var $fin              = null;
  var $unique_chir      = null;
  var $temps_inter_op   = null;
  var $max_intervention = null;
  var $delay_repl       = null;
  var $actes_locked     = null;
    
  // Form Fields
  var $_day          = null;
  var $_month        = null;
  var $_year         = null;
  var $_heuredeb     = null;
  var $_minutedeb    = null;
  var $_heurefin     = null;
  var $_minutefin    = null;
  var $_min_inter_op = null;
  var $_duree_prevue = null;
  
  // Object References
  var $_ref_chir       = null;
  var $_ref_anesth     = null;
  var $_ref_spec       = null;
  var $_ref_spec_repl  = null;
  var $_ref_salle      = null;
  var $_ref_operations = null;
  var $_nb_operations  = null;
  var $_nb_operations_placees  = null;
  var $_fill_rate      = null;

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
  	$specs = parent::getProps();
    $specs["chir_id"]          = "ref class|CMediusers";
    $specs["anesth_id"]        = "ref class|CMediusers";
    $specs["spec_id"]          = "ref class|CFunctions";
    $specs["salle_id"]         = "ref notNull class|CSalle";
    $specs["spec_repl_id"]     = "ref class|CFunctions";
    $specs["date"]             = "date notNull";
    $specs["debut"]            = "time notNull";
    $specs["fin"]              = "time notNull moreThan|debut";
    $specs["unique_chir"]      = "bool default|1";
    $specs["temps_inter_op"]   = "time notNull";
    $specs["max_intervention"] = "num min|0";
    $specs["delay_repl"]       = "num min|0";
    $specs["actes_locked"]     = "bool";

    // TODO: get rid of these form fields !
    $specs["_heuredeb"]        = "num min|0 max|23";
    $specs["_minutedeb"]       = "num min|0 max|59";
    $specs["_heurefin"]        = "num min|0 max|23";
    $specs["_minutefin"]       = "num min|0 max|59";
    $specs["_min_inter_op"]    = "num min|0 max|59";
    return $specs;
  }
  
  function loadRefs($annulee = 1) {
    $this->loadRefsFwd();
    $this->loadRefsBack($annulee);
  }
  
  function loadRefChir($cache = 0) {
    $this->_ref_chir = new CMediusers;
    if($cache) {
      $this->_ref_chir = $this->_ref_chir->getCached($this->chir_id);
    } else {
      $this->_ref_chir->load($this->chir_id);
    }
  }
  
  function loadRefAnesth($cache = 0) {
    $this->_ref_anesth = new CMediusers;
    if($cache) {
      $this->_ref_anesth = $this->_ref_anesth->getCached($this->anesth_id);
    } else {
      $this->_ref_anesth->load($this->anesth_id);
    }
  }
    
  function loadRefSpec($cache = 0) {
    $this->_ref_spec = new CFunctions;
    if($cache) {
      $this->_ref_spec = $this->_ref_spec->getCached($this->spec_id);
    } else {
      $this->_ref_spec->load($this->spec_id);
    }
  }
    
  function loadRefSpecRepl($cache = 0) {
    $this->_ref_spec_repl = new CFunctions;
    if($cache) {
      $this->_ref_spec_repl = $this->_ref_spec->getCached($this->spec_repl_id);
    } else {
      $this->_ref_spec_repl->load($this->spec_repl_id);
    }
  }
  
  function loadRefSalle($cache = 0) {
    $this->_ref_salle = new CSalle;
    if($cache) {
      $this->_ref_salle = $this->_ref_salle->getCached($this->salle_id);
    } else {
      $this->_ref_salle->load($this->salle_id);
    }
  }
  
  function makeView(){
    if($this->chir_id){
      $this->_view = $this->_ref_chir->_view;
    } elseif($this->spec_id){
      $this->_view = $this->_ref_spec->_shortview;
    }
    if($this->anesth_id){
      $this->_view .= " - ".$this->_ref_anesth->_shortview;
    }	
  }

  function loadRefsFwd($cache = 0) {
    $this->loadRefChir($cache);
    $this->loadRefAnesth($cache);
    $this->loadRefSpec($cache);
    $this->loadRefSalle($cache);
    $this->makeView();
  }
  
  function loadRefsOperations($annulee = 1, $order = "rank, horaire_voulu") {
    $where = array();
    $where["plageop_id"] = "= '$this->plageop_id'";
    if(!$annulee) {
      $where["annulee"] = "= '0'";
    }
    $op = new COperation;
    $this->_ref_operations = $op->loadList($where, $order);
    foreach ($this->_ref_operations as &$operation) {
      $operation->_ref_plageop =& $this;
    }
  }

  function loadRefsBack($annulee = 1, $order = "rank, horaire_voulu") {
  	$this->loadRefsOperations($annulee, $order);
  }
  
	/** Mise à jour des horaires en fonction de l'ordre des operations, 
	 *  et mise a jour des rank, de sorte qu'ils soient consecutifs
	 **/
  function reorderOp() {
    $tmpPlage = new CPlageOp;
    $tmpPlage->load($this->_id);
    if(!$this->debut && $this->_id) {
      $this->debut = $tmpPlage->debut;
      $this->temps_inter_op = $tmpPlage->temps_inter_op;
    }
    if(!count($this->_ref_operations)) {
      $this->loadRefsBack(0);
    }
    $new_time = $this->debut;
    $i = 0;
    foreach ($this->_ref_operations as &$op) {
      if($op->rank) {
        $op->rank = ++$i;
        $op->time_operation = $new_time;
        // Pour faire suivre un changement de salle
        if($this->salle_id && $this->salle_id != $tmpPlage->salle_id) {
          $op->salle_id = $this->salle_id;
        }
        $op->updateFormFields();
        $op->store(false);
        $new_time = mbAddTime($op->temp_operation, $new_time); // Durée de l'operation
        $new_time = mbAddTime($this->temps_inter_op, $new_time); // + durée entre les operations
        $new_time = mbAddTime($op->pause, $new_time); // + durée de pause
      }
    }
  }
  

/*
 * returns collision message, null for no collision
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
    foreach ($plages as $key => $plage) {
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
    if(!$this->plageop_id && !$this->chir_id && !$this->spec_id) {
      $msg .= "Vous devez choisir un praticien ou une spécialité<br />";
    }
    return $msg . parent::check();
  }
  
  function store() {
    $this->updateDBFields();
    if ($msg = $this->hasCollisions()) {
      return $msg;
    }
    $oldPlage = new CPlageOp;
    if($this->_id) {
      $oldPlage->load($this->_id);
      $oldPlage->loadRefsBack();
    }
    // Erreur si on est en multi-praticiens, qu'il y a des interventions et qu'on veut mettre un praticien
    if (null !== $this->chir_id && $this->_id && !$this->unique_chir) {
      if(count($oldPlage->_ref_operations) && $oldPlage->spec_id && $this->chir_id) {
        $msg = "Impossible de selectionner un praticien : ".count($oldPlage->_ref_operations)." intervention(s) déjà présentes dans une plage multi-praticiens";
        return $msg;
      }
    }
    
    // Erreur si on change de praticien alors qu'il y a déjà des interventions
    if (null !== $this->chir_id && $this->_id) {
      if(count($oldPlage->_ref_operations) && $oldPlage->chir_id && ($oldPlage->chir_id != $this->chir_id)) {
        $msg = "Impossible de changer le praticien : ".count($oldPlage->_ref_operations)." intervention(s) déjà présentes";
        return $msg;
      }
    }
    // Modification du salle_id de la plage -> repercussion sur les interventions
    if($this->_id && $this->salle_id && $this->salle_id != $oldPlage->salle_id) {
      foreach($oldPlage->_ref_operations as &$_operation) {
        if($_operation->salle_id == $oldPlage->salle_id) {
          $_operation->salle_id = $this->salle_id;
          $_operation->store();
        }
      }
    }
    // Modification du début de la plage ou des minutes entre les interventions
    $this->completeField("debut","temps_inter_op");
    if($this->_id && ($this->debut != $oldPlage->debut || $this->temps_inter_op != $oldPlage->temps_inter_op)) {
      $this->reorderOp();
    }
    return parent::store();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_year         = substr($this->date, 0, 4);
    $this->_month        = substr($this->date, 5, 2);
    $this->_day          = substr($this->date, 8, 2);
    $this->_heuredeb     = substr($this->debut, 0, 2);
    $this->_minutedeb    = substr($this->debut, 3, 2);
    $this->_heurefin     = substr($this->fin, 0, 2);
    $this->_minutefin    = substr($this->fin, 3, 2);
    $this->_min_inter_op = substr($this->temps_inter_op, 3, 2);
    $this->_duree_prevue = mbTimeRelative($this->debut, $this->fin);
  }
  
  function updateDBFields() {
    if(($this->_heuredeb !== null) && ($this->_minutedeb !== null)) {
      $this->debut = $this->_heuredeb.":".$this->_minutedeb.":00";
    }
    if(($this->_heurefin !== null) && ($this->_minutefin !== null)) {
      $this->fin   = $this->_heurefin.":".$this->_minutefin.":00";
    }
    if(($this->_year !== null) && ($this->_month !== null) && ($this->_day !== null)) {
      $this->date = $this->_year."-".$this->_month."-".$this->_day;
    }
    if($this->_min_inter_op !== null) {
      $this->temps_inter_op = "00:$this->_min_inter_op:00";
    }
    return parent::updateDBFields();
  }
  
  function becomeNext() {
    $this->date = mbDate("+7 DAYS", $this->date);
    $where = array();
    $where["date"] = "= '$this->date'";
    $where[] = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    $where["salle_id"] = "= '$this->salle_id'";
    if($this->chir_id) {
      $where["chir_id"] = "= '$this->chir_id'";
    } else {
      $where["spec_id"] = "= '$this->spec_id'";
    }
    $plages           = $this->loadList($where);
    $debut            = $this->debut;
    $fin              = $this->fin;
    $temps_inter_op   = $this->temps_inter_op;
    $max_intervention = $this->max_intervention;
    $anesth_id        = $this->anesth_id;
    $delay_repl       = $this->delay_repl;
    $spec_repl_id     = $this->spec_repl_id;
    $msg = null;
    if(count($plages) > 0) {
      $msg = $this->load(reset($plages)->plageop_id);
    }
    else {
      $this->plageop_id = null;
    }
    if(!$this->chir_id) $this->chir_id = "";
    if(!$this->spec_id) $this->spec_id = "";
    $this->debut            = $debut;
    $this->fin              = $fin;
    $this->temps_inter_op   = $temps_inter_op;
    $this->max_intervention = $max_intervention;
    $this->anesth_id        = $anesth_id;
    $this->delay_repl       = $delay_repl;
    $this->spec_repl_id     = $spec_repl_id;
    $this->updateFormFields();
    return $msg;
  }
  
  function getNbOperations($addedTime = null, $useTimeInterOp = true) {
    if($useTimeInterOp == true){
      $select_time = "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`) + TIME_TO_SEC(`plagesop`.`temps_inter_op`)) AS time";
    }else{
      $select_time = "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`)) AS time";
    }
        
    $sql = "SELECT COUNT(`operations`.`operation_id`) AS total, $select_time
        FROM `operations`, `plagesop`
        WHERE `operations`.`plageop_id` = '$this->plageop_id'
        AND `operations`.`plageop_id` = `plagesop`.`plageop_id`
        AND `operations`.`annulee` = '0'";
    $result = $this->_spec->ds->loadHash($sql);
    $this->_nb_operations = $result["total"];
    if($addedTime){
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
  }
  
  function getPerm($permType) {
    // Chargement
    if(!$this->_ref_salle){
      $this->loadRefSalle();
    }
    if($this->chir_id && !$this->_ref_chir){
      $this->loadRefChir();
    }
    if($this->spec_id && !$this->_ref_spec){
      $this->loadRefSpec();
    }

    //Test de Permission
    if($this->chir_id) {
      $pratPerm = $this->_ref_chir->getPerm($permType);
    } elseif($this->spec_id) {
      $pratPerm = $this->_ref_spec->getPerm($permType);
    }
    
    return ($this->_ref_salle->getPerm($permType) && $pratPerm);
  }
}

$pcConfig = CAppUI::conf("dPbloc CPlageOp");

CPlageOp::$hours_start = str_pad(CValue::first($pcConfig["hours_start"], "08"),2,"0",STR_PAD_LEFT);
CPlageOp::$hours_stop  = str_pad(CValue::first($pcConfig["hours_stop"], "20"),2,"0",STR_PAD_LEFT);
CPlageOp::$minutes_interval = CValue::first($pcConfig["minutes_interval"],"15");

$listHours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
$listMins  = range(0, 59, CPlageOp::$minutes_interval);

foreach($listHours as $key => $hour){
	CPlageOp::$hours[$hour] = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

foreach($listMins as $key => $min){
	CPlageOp::$minutes[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}
	
?>