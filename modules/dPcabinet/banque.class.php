<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

class CBanque extends CMbObject {
  // DB Table key
  var $banque_id = null;

  // DB fields
  var $nom         = null;
  var $description = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'banque';
    $spec->key   = 'banque_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "nom"         => "notNull str",
      "description" => "str"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getSeeks() {
    return array (
      "description" => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

}

?>