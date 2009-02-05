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
  	$specs = parent::getSpecs();
    $specs["nom"]         = "str notNull";
    $specs["description"] = "str";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "description" => "like"
    );
  }
  
	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs['users']      = 'CMediusers banque_id';
	  $backRefs['reglements'] = 'CReglement banque_id';
	  return $backRefs;
	}

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}

?>