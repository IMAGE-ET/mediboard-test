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
  
  // Form fields
  var $_level = null;
  
  function CCatalogueLabo() {
    $this->CMbObject("catalogue_labo", "catalogue_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "pere_id"     => "ref class|CCatalogueLabo",
      "identifiant" => "str notNull",
      "libelle"     => "str notNull"
    );
  }
  
  function updateFormFields() {
    $this->_shortview = $this->identifiant;
    $this->_view = $this->identifiant." : ".$this->libelle;
  }
  
  function loadRefsFwd() {
    $this->_ref_pere = new CCatalogueLabo;
    $this->_ref_pere->load($this->pere_id);
  }
  
  function loadRefsBack() {
    $examen = new CExamenLabo;
    $where = array("catalogue_labo_id" => "= $this->catalogue_labo_id");
    $order = "identifiant";
    $this->_ref_examens_labo = $examen->loadList($where, $order);
    $catalogue = new CCatalogueLabo;
    $where = array("pere_id" => "= $this->catalogue_labo_id");
    $order = "identifiant";
    $this->_ref_catalogues_labo = $catalogue->loadList($where, $order);
  }
  
  function loadRefsDeep($n = 0) {
    $this->_level = $n;
    $this->loadRefs();
    foreach($this->_ref_catalogues_labo as $key => $curr_catalogue) {
      $this->_ref_catalogues_labo[$key]->loadRefsDeep($this->_level + 1);
    }
  }
}

?>