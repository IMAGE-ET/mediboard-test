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
  /*
   * Spécifications. Indique les formats des différents éléments et références de la classe.
   */
  function getSpecs() {
    $specs= parent::getSpecs();
    $specs["marque"] = "notNull str maxLength|50";
    $specs["modele"] = "notNull str maxLength|50";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->marque $this->modele" ;
  }

}
?>