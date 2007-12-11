<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CCatalogueLabo extends CMbObject {
  // DB Table key
  var $catalogue_labo_id = null;
  
  // DB References
  var $pere_id = null;
  
  // DB fields
  var $identifiant = null;
  var $libelle     = null;
  
  // Fwd References
  var $_ref_pere = null;
  
  // Back references
  var $_ref_examens_labo = null;
  var $_ref_catalogues_labo = null;
  
  // Distant references
  var $_ref_prescription_items = null;
  var $_count_examens_labo = null;
  var $_total_examens_labo = null;
  
  // Form fields
  var $_level = null;
  
  function CCatalogueLabo() {
    $this->CMbObject("catalogue_labo", "catalogue_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_locked =& $this->_external;
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if ($this->hasAncestor($this)) {
      return "Cyclic catalog creation";
    }
    
    // Checks whether there is a sibling catalogue in the same hierarchy
    $root = $this->getRootCatalogue();
    foreach ($this->getSiblings() as $_sibling) {
      $_root = $_sibling->getRootCatalogue();
      if ($root->_id == $_root->_id) {
        return "CCatalogue-sibling-conflict ($this->identifiant)";
      }
    }
  }
  
  /**
   * Recursive root catalogue accessor
   */
  function getRootCatalogue() {
    if (!$this->pere_id) {
      return $this;
    }

    $this->loadParent();
    return $this->_ref_pere->getRootCatalogue();
  }
  
  /**
   * load catalogues with same identifier
   */
  function getSiblings() {
    $catalogue = new CCatalogueLabo;
    $where = array();
    $where["identifiant"] = "= '$this->identifiant'";
    $where["catalogue_labo_id"] = "!= '$this->catalogue_labo_id'";
    return $catalogue->loadList($where);
  }
  
  /**
   * Checks whether given catalogue is an ancestor
   */
  function hasAncestor($catalogue) {
    if (!$this->_id) {
      return false;
    }

    if ($catalogue->_id == $this->pere_id) {
      return true;
    }
    
    $this->loadParent();
    return $this->_ref_pere->hasAncestor($catalogue);
  }
  
  function getSpecs() {
    return array (
      "pere_id"     => "ref class|CCatalogueLabo",
      "identifiant" => "str maxLength|10 notNull",
      "libelle"     => "str notNull"
    );
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["catalogues_labo"] = "CCatalogueLabo pere_id";
    $backRefs["examens_labo"   ] = "CExamenLabo catalogue_labo_id";
    return $backRefs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->identifiant;
    $this->_view = $this->libelle;
  }
  
  function computeLevel() {
    if (!$this->pere_id) {
      return $this->_level = 0;
    }
    
    $this->loadParent();
    return $this->_level = $this->_ref_pere->computeLevel() + 1;
  }
  
  function loadParent() {
    if (!$this->_ref_pere) {
      $this->_ref_pere = new CCatalogueLabo;
      $this->_ref_pere->load($this->pere_id);
    }
  }

  function loadRefsFwd() {
    $this->loadParent();
  }

  function loadSections() {
    $this->_ref_catalogues_labo = $this->loadBackRefs("catalogues_labo", "libelle");
  }
  
  function loadExamens() {
    $this->_ref_examens_labo = $this->loadBackRefs("examens_labo");
  }
  
  function loadRefsBack() {
    parent::loadRefsBack();
    
    $this->loadSections();
    $this->loadExamens();
  }
  
  function loadRefsDeep($n = 0) {
    $this->_level = $n;
    $this->loadParent();
    $this->_count_examens_labo = $this->countBackRefs("examens_labo");
    $this->_total_examens_labo = $this->_count_examens_labo;
    $this->loadSections();

    foreach ($this->_ref_catalogues_labo as &$_catalogue) {
      $_catalogue->_ref_pere =& $this;
      $_catalogue->loadRefsDeep($this->_level + 1);
      $this->_total_examens_labo += $_catalogue->_total_examens_labo;
    }
  }
}

?>