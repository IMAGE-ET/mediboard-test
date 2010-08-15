<?php

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"]  = "CSejour prestation_id";
    return $backProps;
  }
    
  function getProps(){
  	$specs = parent::getProps();
    $specs["group_id"] = "ref notNull class|CGroups";
    $specs["nom"] = "str notNull seekable";
    $specs["description"] = "text confidential seekable";
    return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
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