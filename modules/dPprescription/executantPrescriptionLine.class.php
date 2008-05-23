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
  

  function CExecutantPrescriptionLine() {
    $this->CMbObject("executant_prescription_line", "executant_prescription_line_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "category_prescription_id" => "ref class|CCategoryPrescription notNull",
      "nom"         => "str notNull",
      "description"  => "text"
    );
    return array_merge($specsParent, $specs);
  }
  
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;	
  }
  
  function loadRefCategory(){
  	$this->_ref_category = new CCategoryPrescription();
  	$this->_ref_category->load($this->category_id);
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