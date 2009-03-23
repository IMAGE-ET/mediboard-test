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
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]         = "str notNull";
    $specs["description"] = "str";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "description" => "like"
    );
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps['users']      = 'CMediusers banque_id';
	  $backProps['reglements'] = 'CReglement banque_id';
	  return $backProps;
	}

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}

?>