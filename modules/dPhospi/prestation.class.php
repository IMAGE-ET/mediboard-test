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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prestation';
    $spec->key   = 'prestation_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["sejours"]  = "CSejour prestation_id";
    return $backRefs;
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
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefGroup(){
  	$this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsFwd(){
    $this->loadRefGroup();
  }
  
  /**
   * Niveaux de prestations pour l'établissement courant
   * @return array[CPrestation]
   */
  static function loadCurrentList() {
    global $g;
    $prestation = new CPrestation();
    $prestation->group_id = $g;
    return $prestation->loadMatchingList("nom");    
  }
}

?>