<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CFunctionCategoryPrescription class
 */
class CFunctionCategoryPrescription extends CMbObject {
  // DB Table key
  var $function_category_prescription_id = null;
  
  // DB Fields
  var $function_id = null;
  var $category_prescription_id = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'function_category_prescription';
    $spec->key   = 'function_category_prescription_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["function_id"]  = "notNull ref class|CFunctions";
    $specs["category_prescription_id"] = "notNull ref class|CCategoryPrescription";
    return $specs;
  }
  
  static function getAllUserExecutants(){
  	$function_categorie = new CFunctionCategoryPrescription();
  	$user_executants = array();
  	$functions = array();
  	$associations = $function_categorie->loadList();
    $functions_by_cat = array();
  	foreach($associations as $_association){
  	  if(!array_key_exists($_association->function_id, $functions)){
	  	  $function = new CFunctions();
	  	  $function->load($_association->function_id);
	  	  $function->loadRefsUsers();
	  		$functions_by_cat[$_association->category_prescription_id][$_association->function_id] = $function;
  	  }
  	}
  	foreach($functions_by_cat as $category_id => $_functions){
  	  foreach($_functions as $_function){
  	    foreach($_function->_ref_users as $_user){
  	      $user_executants[$category_id][$_user->_id] = $_user;
  	    }
  	  }
  	}
  	return $user_executants;
  }
  
  function loadRefFunction(){
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
}
  
?>