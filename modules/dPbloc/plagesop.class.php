<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author Romain Ollivier
 */

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("dPbloc"   , "salle"));

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
  var $date  = null;
  var $debut = null;
  var $fin   = null;
    
  // Form Fields
  var $_day       = null;
  var $_month     = null;
  var $_year      = null;
  var $_heuredeb  = null;
  var $_minutedeb = null;
  var $_heurefin  = null;
  var $_minutefin = null;
  
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

    $this->_props["chir_id"]   = "ref";
    $this->_props["anesth_id"] = "ref";
    $this->_props["spec_id"]   = "ref|xor|chir_id";
    $this->_props["salle_id"]  = "ref|notNull";
    $this->_props["date"]      = "date|notNull";
    $this->_props["debut"]     = "time|notNull";
    $this->_props["fin"]       = "time|notNull";
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
      $this->_view .= " &mdash; ".$this->_ref_anesth->_shortview;
    }
  }
  
  function loadRefsBack($annulee = 1) {
    if($annulee)
      $sql = "SELECT * FROM operations WHERE plageop_id = '$this->plageop_id' order by rank";
    else
      $sql = "SELECT * FROM operations WHERE plageop_id = '$this->plageop_id' and annulee = '0' order by rank";
    $this->_ref_operations = db_loadObjectList($sql, new COperation);
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label" => "Opérations", 
      "name" => "operations", 
      "idfield" => "operation_id", 
      "joinfield" => "plageop_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }

/*
 * returns collision message, null for no collision
 */
  function hasCollisions() {
    // Get all other plages the same day
    $sql = "SELECT debut, fin" .
        "\nFROM plagesop" .
        "\nWHERE salle_id = '$this->salle_id'" .
        "\nAND date = '$this->date'" .
        "\nAND plageop_id != '$this->plageop_id'";
    $row = db_loadlist($sql);
    $msg = null;
    foreach ($row as $key => $value) {
      if (($value["debut"] < $this->fin and $value["fin"] > $this->fin)
        or($value["debut"] < $this->debut and $value["fin"] > $this->debut)
        or($value["debut"] >= $this->debut and $value["fin"] <= $this->fin)) {
        $msg .= "Collision avec la plage du $this->date, de {$value['debut']} à {$value['fin']}. ";
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
    $this->_year      = substr($this->date, 0, 4);
    $this->_month     = substr($this->date, 5, 2);
    $this->_day       = substr($this->date, 8, 2);
    $this->_heuredeb  = substr($this->debut, 0, 2);
    $this->_minutedeb = substr($this->debut, 3, 2);
    $this->_heurefin  = substr($this->fin, 0, 2);
    $this->_minutefin = substr($this->fin, 3, 2);
  }
  
  function updateDBFields() {
    if(($this->_heuredeb !== null) && ($this->_minutedeb !== null))
      $this->debut = $this->_heuredeb.":".$this->_minutedeb.":00";
    if(($this->_heurefin !== null) && ($this->_minutefin !== null))
      $this->fin   = $this->_heurefin.":".$this->_minutefin.":00";
    if(($this->_year !== null) && ($this->_month !== null) && ($this->_day !== null)) {
      $this->date = $this->_year."-".$this->_month."-".$this->_day;
    }
  }
  
  function becomeNext() {
    $this->date = mbDate("+7 DAYS", $this->date);
    $sql = "SELECT plageop_id" .
      "\nFROM plagesop" .
      "\nWHERE date = '$this->date'" .
      "\nAND salle_id = '$this->salle_id'" .
      ($this->chir_id ? "\nAND chir_id = '$this->chir_id'" : "\nAND spec_id = '$this->spec_id'");
    $row = db_loadlist($sql);
    $debut = $this->debut;
    $fin = $this->fin;
    $msg = null;
    if(count($row) > 0)
      $msg = $this->load($row[0]["plageop_id"]);
    else
      $this->plageop_id = null;
    $this->debut = $debut;
    $this->fin = $fin;
    $this->updateFormFields();
    return $msg;
  }
  
  function GetNbOperations(){
    $sql = "SELECT COUNT(operation_id) AS total," .
        "\nSUM(TIME_TO_SEC(temp_operation)) AS time" .
        "\nFROM operations" .
        "\nWHERE plageop_id = '$this->plageop_id' AND annulee = '0'";
    $result = null;
    db_loadHash($sql, $result);
    $this->_nb_operations = $result["total"];
    $this->_fill_rate = number_format($result["time"]*100/(strtotime($this->fin)-strtotime($this->debut)), 2);
  }
  
  function canRead($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canRead = $this->_ref_salle->canRead();
    if($this->chir_id) {
      $pratCanRead = $this->_ref_chir->canRead();
    } elseif($this->spec_id) {
      $pratCanRead = $this->_ref_spec->canRead();
    }
    $this->_canRead = $this->_ref_salle->canRead() && $pratCanRead;
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canEdit = $this->_ref_salle->canEdit();
    if($this->chir_id) {
      $pratCanEdit = $this->_ref_chir->canEdit();
    } elseif($this->spec_id) {
      $pratCanEdit = $this->_ref_spec->canEdit();
    }
    $this->_canEdit = $this->_ref_salle->canEdit() && $pratCanEdit;
    return $this->_canEdit;
  }
}

?>