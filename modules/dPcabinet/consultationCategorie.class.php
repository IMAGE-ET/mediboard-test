<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:  $
* @author Alexis Granger
*/

class CConsultationCategorie extends CMbObject {
	
  // DB Table key
  var $categorie_id = null;

  // DB References
  var $function_id = null;
   
  // DB fields
  var $nom_categorie = null;
  var $nom_icone     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_cat';
    $spec->key   = 'categorie_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["function_id"]   = "ref notNull class|CFunctions";
    $specs["nom_categorie"] = "str notNull";
    $specs["nom_icone"]     = "str notNull";
    return $specs;
  }
  
	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["consultations"] = "CConsultation categorie_id";
	  return $backRefs;
	}

  function updateFormFields() {
  	parent::updateFormFields();
    $this->_view = $this->nom_categorie;
  }  
}

?>