<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CExecutant class
 */
class CExecutantPrescriptionLine extends CMbObject {
  
	// Table key
	var $executant_prescription_line_id = null;

  // DB Fields
  var $category_prescription_id = null;
  var $nom         = null;
  var $description  = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'executant_prescription_line';
    $spec->key   = 'executant_prescription_line_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["category_prescription_id"] = "ref class|CCategoryPrescription notNull";
    $specs["nom"]         = "str notNull";
    $specs["description"] = "text";
    return $specs;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_line_element"]   = "CPrescriptionLineElement executant_prescription_line_id";
    $backRefs["prescription_line_comment"]   = "CPrescriptionLineComment executant_prescription_line_id";
    return $backRefs;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;	
  }
  
  function loadRefCategory() {
    $category = new CCategoryPrescription();
  	$this->_ref_category_prescription = $category->getCached($this->category_prescription_id);	
  }
    
  static function getAllExecutants(){
  	$executant = new CExecutantPrescriptionLine();
  	$executants = array();
  	$_executants = $executant->loadList();
  	foreach($_executants as $_executant){
  		$executants[$_executant->category_prescription_id][] = $_executant;
  	}
  	return $executants;
  }
}

?>