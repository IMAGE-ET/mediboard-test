<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author Romain Ollivier
 */

/**
 * The plagesop Class
 */
class CPlageOp extends CMbObject {
  // DB Table key
  var $plageop_id = null;
  
  // DB References
  var $chir_id   = null;
  var $anesth_id = null;
  var $spec_id   = null;
  var $salle_id  = null;

  // DB fields
  var $date           = null;
  var $debut          = null;
  var $fin            = null;
  var $temps_inter_op = null;
    
  // Form Fields
  var $_day          = null;
  var $_month        = null;
  var $_year         = null;
  var $_heuredeb     = null;
  var $_minutedeb    = null;
  var $_heurefin     = null;
  var $_minutefin    = null;
  var $_min_inter_op = null;
  
  // Object References
  var $_ref_chir       = null;
  var $_ref_anesth     = null;
  var $_ref_spec       = null;
  var $_ref_salle      = null;
  var $_ref_operations = null;
  var $_nb_operations  = null;
  var $_fill_rate      = null;

  function CPlageOp() {
    $this->CMbObject("plagesop", "plageop_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "chir_id"        => "ref",
      "anesth_id"      => "ref",
      "spec_id"        => "ref|xor|chir_id",
      "salle_id"       => "notNull refMandatory",
      "date"           => "notNull date",
      "debut"          => "notNull time",
      "fin"            => "notNull time",
      "temps_inter_op" => "notNull time",
    );
  }
  
  function loadRefs($annulee = 1) {
    $this->loadRefsFwd();
    $this->loadRefsBack($annulee);
  }
  
  function loadRefChir() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
  }
  
  function loadRefAnesth() {
    $this->_ref_anesth = new CMediusers;
    $this->_ref_anesth->load($this->anesth_id);
  }
  
  function loadRefSpec() {
    $this->_ref_spec = new CFunctions;
    $this->_ref_spec->load($this->spec_id);
  }
  
  function loadRefSalle() {
    $this->_ref_salle = new CSalle;
    $this->_ref_salle->load($this->salle_id);
  }

  function loadRefsFwd() {
    $this->loadRefChir();
    $this->loadRefAnesth();
    $this->loadRefSpec();
    $this->loadRefSalle();
    if($this->chir_id){
      $this->_view = "Dr. ".$this->_ref_chir->_view;
    } elseif($this->spec_id){
      $this->_view = $this->_ref_spec->_shortview;
    }
    if($this->anesth_id){
      $this->_view .= " - ".$this->_ref_anesth->_shortview;
    }
  }
  
  function loadRefsBack($annulee = 1, $order = "rank") {
    $where = array();
    $where["plageop_id"] = "= '$this->plageop_id'";
    if(!$annulee) {
      $where["annulee"] = "= '0'";
    }
    $op = new COperation;
    $this->_ref_operations = $op->loadList($where, $order);
  }
  
  function reorderOp() {
    if(!$this->debut && $this->_id) {
      $tmpPlage = new CPlageOp;
      $tmpPlage->load($this->_id);
      $this->debut = $tmpPlage->debut;
      $this->temps_inter_op = $tmpPlage->temps_inter_op;
    }
    if(!count($this->_ref_operations)) {
      $this->loadRefsBack(0);
    }
    $new_time = $this->debut;
    $i = 0;
    foreach ($this->_ref_operations as $keyOp => $op) {
      if($this->_ref_operations[$keyOp]->rank) {
        $i++;
        $this->_ref_operations[$keyOp]->rank = $i;
        $this->_ref_operations[$keyOp]->time_operation = $new_time;
        $this->_ref_operations[$keyOp]->updateFormFields();
        $this->_ref_operations[$keyOp]->store(true, false);
        $new_time = mbAddTime($op->temp_operation, $new_time);
        $new_time = mbAddTime($this->temps_inter_op, $new_time);
        $new_time = mbAddTime($op->pause, $new_time);
      }
    }
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "Opérations", 
      "name"      => "operations", 
      "idfield"   => "operation_id", 
      "joinfield" => "plageop_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }

/*
 * returns collision message, null for no collision
 */
  function hasCollisions() {
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
    if(!$this->plageop_id) {
      if (!$this->chir_id && !$this->spec_id) {
        $msg .= "Vous devez choisir un praticien ou une spécialité<br />";
      }
    }
    return $msg . parent::check();
  }
  
  function store () {
    $this->updateDBFields();
    if ($msg = $this->hasCollisions()) {
      return $msg;
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
    $this->reorderOp(); 
  }
  
  function becomeNext() {
    $this->date = mbDate("+7 DAYS", $this->date);
    $where = array();
    $where["date"] = "= '$this->date'";
    $where["salle_id"] = "= '$this->salle_id'";
    if($this->chir_id) {
      $where["chir_id"] = "= '$this->chir_id'";
    } else {
      $where["spec_id"] = "= '$this->spec_id'";
    }
    $plages = $this->loadList($where);
    $debut = $this->debut;
    $fin = $this->fin;
    $msg = null;
    if(count($plages) > 0)
      $msg = $this->load(reset($plages)->plageop_id);
    else
      $this->plageop_id = null;
    $this->debut = $debut;
    $this->fin = $fin;
    $this->updateFormFields();
    return $msg;
  }
  
  function GetNbOperations() {
    $sql = "SELECT COUNT(`operations`.`operation_id`) AS total," .
        "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`) + TIME_TO_SEC(`plagesop`.`temps_inter_op`)) AS time" .
        "\nFROM `operations`, `plagesop`" .
        "\nWHERE `operations`.`plageop_id` = '$this->plageop_id'" .
        "\nAND `operations`.`plageop_id` = `plagesop`.`plageop_id`" .
        "\nAND `operations`.`annulee` = '0'";
    $result = null;
    db_loadHash($sql, $result);
    $this->_nb_operations = $result["total"];
    $this->_fill_rate = number_format($result["time"]*100/(strtotime($this->fin)-strtotime($this->debut)), 2);
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

?>