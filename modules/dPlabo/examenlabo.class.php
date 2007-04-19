<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CExamenLabo extends CMbObject {
  // DB Table key
  var $examen_labo_id = null;
  
  // DB References
  var $catalogue_labo_id = null;
  
  // DB fields
  var $identifiant = null;
  var $libelle     = null;
  var $type        = null;
  var $unite       = null;
  var $min         = null;
  var $max         = null;
  
  // Form fields
  var $_reference_values = null;
  
  // Fwd References
  var $_ref_catalogue_labo = null;
  
  // Back References
  var $_ref_items_pack_labo = null;

  // Distant References
  var $_ref_packs_labo = null;
  
  function CExamenLabo() {
    $this->CMbObject("examen_labo", "examen_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "catalogue_labo_id" => "ref class|CCatalogueLabo notNull",
      "identifiant"       => "str notNull",
      "libelle"           => "str notNull",
      "type"              => "enum list|bool|num|str notNull",
      "unite"             => "str",
      "min"               => "float",
      "max"               => "float moreThan|min"
    );
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["items_pack_labo"] = "CPackItemExamenLabo examen_labo_id";
    return $backRefs;
  }

  function updateFormFields() {
    $this->_shortview = $this->identifiant;
    $this->_view = "$this->identifiant : $this->libelle";
    
    if ($this->type == "num") {
      $this->_reference_values = "$this->min $this->unite - $this->max $this->unite";
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_catalogue_labo = new CCatalogueLabo;
    $this->_ref_catalogue_labo->load($this->catalogue_labo_id);
  }
  
  function loadRefsBack() {
    $this->_ref_packs_labo = array();
    $item = new CPackItemExamenLabo;
    $item->examen_labo_id = $this->_id;
    $this->_ref_items_pack_labo = $item->loadMatchingList();
    foreach ($this->_ref_items_pack_labo as &$item) {
      $item->loadRefPack();
      $pack =& $item->_ref_pack_examens_labo;
      $this->_ref_packs_labo[$pack->_id] = $pack;
    }
  }
}

?>