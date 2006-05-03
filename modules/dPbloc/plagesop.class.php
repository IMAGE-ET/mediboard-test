<?php /* $Id: plagesop.class.php,v 1.28 2005/09/21 19:18:41 rhum1 Exp $ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: 1.28 $
 * @author Romain Ollivier
 */

require_once( $AppUI->getSystemClass ('mbobject' ) );

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("dPbloc", "salle"));

/**
 * The plagesop Class
 */
class CPlageOp extends CMbObject {
  // DB Table key
  var $id = null;
  
  // DB References
  var $chir_id = null;
  var $anesth_id = null;
  var $id_chir = null;
  var $id_anesth = null;
  var $id_spec = null;
  var $id_salle = null;

  // DB fields
  var $date = null;
  var $debut = null;
  var $fin = null;
    
  // Form Fields
  var $_date = null;
  var $_day = null;
  var $_month = null;
  var $_year = null;
  var $_heuredeb = null;
  var $_minutedeb = null;
  var $_heurefin = null;
  var $_minutefin = null;
  
  // Object Refernces
  var $_ref_chir = null;
  var $_ref_anesth = null;
  var $_ref_spec = null;
  var $_ref_salle = null;
  var $_ref_operations = null;

  function CPlageOp() {
    $this->CMbObject( 'plagesop', 'id' );

    $this->_props["chir_id"]   = "ref";
    $this->_props["anesth_id"] = "ref";
    $this->_props["id_chir"]   = "str";
    $this->_props["id_anesth"] = "str";
    $this->_props["id_spec"]   = "ref";
    $this->_props["id_salle"]  = "ref|notNull";
    $this->_props["date"]      = "date|notNull";
    $this->_props["debut"]     = "time|notNull";
    $this->_props["fin"]       = "time|notNull";
  }
  
  function loadRefs($annulee = 1) {
    $this->loadRefsFwd();
    $this->loadRefsBack($annulee);
  }

  function loadRefsFwd() {
    // Forward references
    
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
    
    $this->_ref_anesth = new CMediusers;
    $this->_ref_anesth->load($this->anesth_id);

    $this->_ref_spec = new CFunctions;
    $this->_ref_spec->load($this->id_spec);

    $this->_ref_salle = new CSalle;
    $this->_ref_salle->load($this->id_salle);
  }
  
  function loadRefsBack($annulee = 1) {
    // Backward references
    if($annulee)
      $sql = "SELECT * FROM operations WHERE plageop_id = '$this->id' order by rank";
    else
      $sql = "SELECT * FROM operations WHERE plageop_id = '$this->id' and annulee = '0' order by rank";
    $this->_ref_operations = db_loadObjectList($sql, new COperation);
  }

  // Overload canDelete
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'Opérations', 
      'name' => 'operations', 
      'idfield' => 'operation_id', 
      'joinfield' => 'plageop_id'
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }

/*
 * returns collision message, null for no collision
 */
  function hasCollisions() {
    // Get all other plages the same day
    $sql = "SELECT * FROM plagesop " .
        "WHERE id_salle = '$this->id_salle' " .
        "AND date = '$this->date' " .
        "AND id != '$this->id'";
    $row = db_loadlist($sql);
    $msg = null;
    foreach ($row as $key => $value) {
      if (($value['debut'] < $this->fin and $value['fin'] > $this->fin)
        or($value['debut'] < $this->debut and $value['fin'] > $this->debut)
        or($value['debut'] >= $this->debut and $value['fin'] <= $this->fin)) {
        $msg .= "Collision avec la plage du $this->date, de {$value['debut']} à {$value['fin']}. ";
      }
    }
    return $msg;   
  }

  function check() {
    // Data checking
    $msg = null;

    if(!$this->id) {
      if (!$this->chir_id && !$this->id_spec) {
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
    $this->_year  = substr($this->date, 0, 4);
    $this->_month = substr($this->date, 5, 2);
    $this->_day   = substr($this->date, 8, 2);
    $this->_date = "$this->_day/$this->_month/$this->_year";
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
      $this->_date = $this->_day."/".$this->_month."/".$this->_year;
    }
  }
  
  function becomeNext() {
    $this->date = mbDate("+7 DAYS", $this->date);
    $sql = "SELECT id" .
      "\nFROM plagesop" .
      "\nWHERE date = '{$this->date}'" .
      "\nAND id_salle = '{$this->id_salle}'" .
      ($this->chir_id ? "\nAND chir_id = '$this->chir_id'" : "\nAND id_spec = '$this->id_spec'");
    $row = db_loadlist($sql);
    $debut = $this->debut;
    $fin = $this->fin;
    $msg = null;
    if(count($row) > 0)
      $msg = $this->load($row[0]["id"]);
    else
      $this->id = null;
    $this->debut = $debut;
    $this->fin = $fin;
    $this->updateFormFields();
    return $msg;
  }    
}
?>