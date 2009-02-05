<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

class CCellSaver extends CMbObject {
	
	 //DB Table Key
	var $cell_saver_id = null;
	
	//DB Fields 
	var $marque = null;
	var $modele = null;
	
 function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cell_saver';
    $spec->key   = 'cell_saver_id';
    return $spec;
  }

  function getSpecs() {
    $specs= parent::getSpecs();
    $specs["marque"] = "str notNull maxLength|50";
    $specs["modele"] = "str notNull maxLength|50";
    return $specs;
  }
  
	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["blood_salvages"] = "CBloodSalvage cell_saver_id";
	  return $backRefs;
	}	
	
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->marque $this->modele" ;
  }

}
?>