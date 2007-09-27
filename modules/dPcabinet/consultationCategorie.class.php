<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI;

class CConsultationCategorie extends CMbObject {
	
  // DB Table key
  var $categorie_id = null;

  // DB References
  var $function_id = null;
   
  // DB fields
  var $nom_categorie = null;
  var $nom_icone     = null;
  
  
  function CConsultationCategorie() {
    $this->CMbObject("consultation_cat", "categorie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["function_id"]   = "notNull ref class|CFunctions";
    $specs["nom_categorie"] = "notNull str";
    $specs["nom_icone"]     = "notNull str";
    return $specs;
  }
  
  
  function updateFormFields() {
  	parent::updateFormFields();
    $this->_view = $this->nom_categorie;
  }  
}

?>