<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["function_id"]  = "ref notNull class|CFunctions";
    $specs["category_prescription_id"] = "ref notNull class|CCategoryPrescription";
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
  
  function check(){
    if($this->countSiblings()){
      return "Association deja présente dans la table";  
    }
    return parent::check();  
  }
  
  function countSiblings() {
    $function_category = new CFunctionCategoryPrescription();
    $function_category->function_id = $this->function_id;
    $function_category->category_prescription_id = $this->category_prescription_id;
    return $function_category->countMatchingList();
  }
  
  function canDeleteEx(){
    $this->completeField("function_id");
    $this->completeField("category_prescription_id");
    
    // Chargement des users de la fonction
    $this->loadRefFunction();
    $this->_ref_function->loadRefsUsers();
    $users_id = array_keys($this->_ref_function->_ref_users);
    
    // Calcul du nombre de lignes d'elements
    $prescription_line_element = new CPrescriptionLineElement();
    $where = array();
    $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
    $where["element_prescription.category_prescription_id"] = " = '$this->category_prescription_id'";
    $where["prescription_line_element.user_executant_id"] = CSQLDataSource::prepareIn($users_id);
    $count_lines_element = $prescription_line_element->countList($where, null, null, null, $ljoin);
    
    // Calcul du nombre de lignes de commentaires
    $prescription_line_comment = new CPrescriptionLineComment();
    $where = array();
    $where["category_prescription_id"] = " = '$this->category_prescription_id'";
    $where["user_executant_id"] = CSQLDataSource::prepareIn($users_id);
    $count_lines_comment = $prescription_line_comment->countList($where);
    
    $count = $count_lines_element + $count_lines_comment;
    if($count > 0){
      return "Cet Objet est lié à $count lignes de prescription";
    }
    return parent::canDeleteEx();
  }
}

?>