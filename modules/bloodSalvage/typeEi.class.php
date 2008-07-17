<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

class CTypeEi extends CMbObject {
	
	//DB Table key
	var $type_ei_id = null;
	
	//DB Fields 
	var $name = null;
	var $concerne = null;
	var $desc = null;
	
function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_ei';
    $spec->key   = 'type_ei_id';
    return $spec;
  }
  
  /*
   * Spécifications. Indique les formats des différents éléments et références de la classe.
   */
  function getSpecs() {
    $specs= parent::getSpecs();
    $specs["name"]     = "notNull str maxLength|30";
    $specs["concerne"] = "notNull enum list|pat|vis|pers|med|mat";
    $specs["desc"]     = "text";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }
  
}
?>