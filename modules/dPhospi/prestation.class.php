<?php

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Alexis Granger
*/

class CPrestation extends CMbObject {
  // DB Table key
  var $prestation_id = null;
  
  // DB references
  var $group_id = null;
  
  // DB fields
  var $nom = null;
  var $description = null;
  
  function CPrestation(){
  	$this->CMbObject("prestation","prestation_id");
  	
  	$this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs(){
  	$specs = parent::getSpecs();
    $specs["group_id"] = "notNull ref class|CGroups";
    $specs["nom"] = "notNull str";
    $specs["description"] = "text confidential";
    return $specs;
  }

  function getSeeks() {
    return array (
      "nom"         => "like",
      "description" => "like"
    );
  }
  
  function updateFormFields(){
    $this->_view = $this->nom;
  }
  
  function loadRefGroup(){
  	$this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsFwd(){
    $this->loadRefGroup();
  }
  
}

?>