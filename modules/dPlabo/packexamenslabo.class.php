<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CPackExamensLabo extends CMbObject {
  // DB Table key
  var $pack_examens_labo_id = null;
  
  // DB references
  var $function_id  = null;
  var $code         = null;
  var $obsolete     = null;
  
  // DB fields
  var $libelle = null;
  
  // Forward references
  var $_ref_function = null;
  
  // Back references
  var $_ref_items_examen_labo = null;
  var $_ref_examens_labo = null;
  
  function CPackExamensLabo() {
    $this->CMbObject("pack_examens_labo", "pack_examens_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_locked =& $this->_external;
  }
  
  function getSpecs() {
    return array (
      "code"         => "num",
      "function_id"  => "ref class|CFunctions",
      "libelle"      => "str notNull",
      "obsolete"     => "bool"
    );
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["items_examen_labo"] = "CPackItemExamenLabo pack_examens_labo_id";
    return $backRefs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->libelle;
    $this->_view      = $this->libelle;
  }
  
  function loadRefsFwd() {
    $this->loadRefFunction();
  }
  
  function loadRefFunction() {
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
  }
  
  function loadRefsItemExamenLabo(){
  	$item = new CPackItemExamenLabo;
  	$ljoin["examen_labo"] = "pack_item_examen_labo.examen_labo_id = examen_labo.examen_labo_id";
  	$where = array("pack_examens_labo_id" => "= '$this->pack_examens_labo_id'");
  	// Permet d'afficher dans le pack seulement les analyses non obsolètes
  	$where["examen_labo.obsolete"] = " = '0'";
    $this->_ref_items_examen_labo = $item->loadList($where, null, null, null, $ljoin);
  }
  
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsItemExamenLabo();
  	$this->_ref_examens_labo = array();
    foreach ($this->_ref_items_examen_labo as &$_item) {
      $_item->loadRefExamen();
      $_item->_ref_pack_examens_labo =& $this;
      $this->_ref_examens_labo[$_item->examen_labo_id] = $_item->_ref_examen_labo;
    }
  }
  
  function getPerm($perm_type) {
    if($this->function_id) {
      $this->loadRefFunction();
      return $this->_ref_function->getPerm($perm_type);
    } else {
      return true;
    }
  }
}

?>