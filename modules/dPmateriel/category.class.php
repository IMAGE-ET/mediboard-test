<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */

/**
 * The CCategory class
 */
class CCategory extends CMbObject {
  // DB Table key
  var $category_id   = null;	
  var $category_name = null;
  
  // Referencies
  var $_ref_materiel = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'materiel_category';
    $spec->key   = 'category_id';
    return $spec;
  }
  
  function getBackProps() {
      $backProps = parent::getBackProps();
      $backProps["materiel"] = "CMateriel category_id";
     return $backProps;
  }
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "category_name" => "str notNull maxLength|50 seekable"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->category_name;
  }
	
  function loadRefsBack(){
    $this->_ref_materiel = new CMateriel;
    $where = array();
    $where["category_id"]="= '$this->category_id'";
    $this->_ref_materiel = $this->_ref_materiel->loadList($where);
  }
  

}
?>