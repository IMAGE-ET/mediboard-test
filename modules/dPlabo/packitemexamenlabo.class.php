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
  
  // Back references
  var $_ref_pack_examens_labo = null;
  var $_ref_examen_labo       = null;
  
  function CPackItemExamenLabo() {
    $this->CMbObject("pack_item_examen_labo", "pack_item_examen_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "pack_examens_labo_id" => "ref class|CPackExamensLabo notNull",
      "examen_labo_id"       => "ref class|CExamenLabo notNull",
      "libelle"              => "str notNull"
    );
  }
  
  function updateFormFields() {
    $this->_shortview = $this->libelle;
    $this->_view      = $this->libelle;
  }
  
  function loadRefsFwd() {
    $this->_ref_pack_examens_labo = new CPackExamensLabo;
    $this->_ref_pack_examens_labo->load($this->pack_examens_labo_id);
    $this->_ref_examen_labo = new CExamenLabo;
    $this->_ref_examen_labo->load($this->examen_labo_id);
  }
  
  function loadRefsBack() {
    $item = new CPackItemExamenLabo;
    $where = array("pack_catalogue_labo_id" => "= $this->pack_catalogue_labo_id");
    $order = "identifiant";
    $this->_ref_items_examen_labo = $item->loadList($where, $order);
    $this->_ref_examens_labo = array();
    foreach($this->_ref_items_examen_labo as $key => $curr_item) {
      $this->_ref_items_examen_labo[$key]->loadRefsFwd();
      $examen_id = $this->_ref_items_examen_labo[$key]->_ref_examen_labo->_id;
      $this->_ref_examens_labo[$examen_id] =& $this->_ref_items_examen_labo[$key]->_ref_examen_labo;
    }
  }
}

?>