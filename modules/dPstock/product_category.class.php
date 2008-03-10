<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductCategory extends CMbObject {
  // DB Table key
  var $category_id   = null;
  
  // DB fields
  var $name          = null;

  // Object References
  //    Multiple
  var $_ref_products = null;

  function CProductCategory() {
    $this->CMbObject('product_category', 'category_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['product'] = 'CProduct category_id';
    return $backRefs;
  }

  function getSpecs() {
    return array (
      'name' => 'notNull str maxLength|50',
    );
  }

  function getSeeks() {
    return array (
      'name' => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsBack();
    $this->_view = $this->name.' ('.count($this->_ref_products).' articles)';
  }

  function loadRefsBack() {
    $where = array();
    $where['category_id'] = "= '$this->category_id'";
    
    $this->_ref_products = new CProduct;
    $this->_ref_products = $this->_ref_products->loadList($where);
  }
}
?>