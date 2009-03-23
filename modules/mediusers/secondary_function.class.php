<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision: 5636 $
 *  @author Romain Ollivier
*/

/**
 * The CSecondaryFunction Class
 */
class CSecondaryFunction extends CMbObject {
  // DB Table key
	var $secondary_function_id = null;

  // DB References
  var $function_id = null;
  var $user_id = null;
  
  // Object References
  var $_ref_function = null;
  var $_ref_users = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'secondary_function';
    $spec->key   = 'secondary_function_id';
    return $spec;
  }
	
  function getProps() {
  	$specs = parent::getProps();
    $specs["function_id"] = "ref notNull class|CFunctions";
    $specs["user_id"]     = "ref notNull class|CMediusers cascade";
    return $specs;
  }
  
  function updateFormFields() {
		parent::updateFormFields();
		$this->loadRefsFwd();
    $this->_view = $this->_ref_user->_view." - ".$this->_ref_function->_view;
    $this->_shortview = $this->_ref_user->_shortview." - ".$this->_ref_function->_shortview;
 	}
  
  // Forward references
  function loadRefsFwd() {
    $this->loadRefFunction();
    $this->loadRefUser();
  }
  
  function loadRefFunction() {
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
  
  function loadRefUser() {
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
}
?>