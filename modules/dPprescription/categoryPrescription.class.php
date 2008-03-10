<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescription class
 */
class CCategoryPrescription extends CMbObject {
  // DB Table key
  var $category_prescription_id = null;
  
  // DB Fields
  var $chapitre    = null;
  var $nom         = null;
  var $description = null;
  
  // BackRefs
  var $_ref_elements_prescription = null;
    
  function CCategoryPrescription() {
    $this->CMbObject("category_prescription", "category_prescription_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "chapitre" => "notNull enum list|dmi|anapath|biologie|imagerie|consult|kine|soin",
      "nom"      => "notNull str",
      "description" => "text"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_view = $this->nom;
  }
  
  function loadCategoriesByChap($chap = null){
  	$category = new CCategoryPrescription();
  	$_categories = array();
  	$where = array();
  	if($chap){
  		$where["chapitre"] = " = '$chap'";
  	}
  	$_categories["dmi"] = array();
  	$_categories["anapath"] = array();
  	$_categories["biologie"] = array();
  	$_categories["imagerie"] = array();
  	$_categories["consult"] = array();
  	$_categories["kine"] = array();
  	$_categories["soin"] = array();
  	
  	$categories = $category->loadList($where);
  	foreach($categories as $_cat){
  		$_categories[$_cat->chapitre][$_cat->_id] = $_cat;
  	}
  	ksort($_categories);
  	return $_categories;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["element_prescription"] = "CElementPrescription category_prescription_id";
    return $backRefs;
  }     
}

?>