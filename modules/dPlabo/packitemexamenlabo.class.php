<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CPackItemExamenLabo extends CMbObject {
  // DB Table key
  var $pack_item_examen_labo_id = null;
  
  // DB references
  var $pack_examens_labo_id = null;
  var $examen_labo_id       = null;
  
  // Forward references
  var $_ref_pack_examens_labo = null;
  var $_ref_examen_labo       = null;
  
  function CPackItemExamenLabo() {
    parent::__construct();
    $this->_locked =& $this->_external;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack_item_examen_labo';
    $spec->key   = 'pack_item_examen_labo_id';
    return $spec;
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    // Check unique item
    $other = new CPackItemExamenLabo;
    $other->pack_examens_labo_id = $this->pack_examens_labo_id;
    $other->examen_labo_id = $this->examen_labo_id;
    $other->loadMatchingObject();
    if ($other->_id && $other->_id != $this->_id) {
      return "$this->_class_name-unique-conflict";
    }
  }

  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "pack_examens_labo_id" => "ref class|CPackExamensLabo notNull",
      "examen_labo_id"       => "ref class|CExamenLabo notNull"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_shortview = $this->_ref_examen_labo->_shortview;
    $this->_view      = $this->_ref_examen_labo->_view;
  }
  
  function loadRefPack() {
    $this->_ref_pack_examens_labo = new CPackExamensLabo;
    $this->_ref_pack_examens_labo->load($this->pack_examens_labo_id);
  }

  function loadRefExamen() {
    $this->_ref_examen_labo = new CExamenLabo;
    $this->_ref_examen_labo->load($this->examen_labo_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefPack();
    $this->loadRefExamen();
  }
}

?>