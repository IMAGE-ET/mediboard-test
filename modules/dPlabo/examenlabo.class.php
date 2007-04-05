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
  
  // Fwd References
  var $_ref_catalogue_labo = null;
  
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
  
  function updateFormFields() {
    $this->_shortview = $this->identifiant;
    $this->_view = $this->identifiant." : ".$this->libelle;
  }
  
  function loadRefsFwd() {
    $this->_ref_catalogue_labo = new CCatalogueLabo;
    $this->_ref_catalogue_labo->load($this->catalogue_labo_id);
  }
}

?>