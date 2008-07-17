<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

class CProductStock extends CMbObject {
  // DB Table key
  var $stock_id                 = null;

  // DB Fields
  var $product_id               = null;
  var $quantity                 = null;

  // Object References
  //    Single
  var $_ref_product             = null;

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'product_id' => 'notNull ref class|CProduct',
      'quantity'   => 'num notNull',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view;
  }

  function loadRefsFwd(){
    $this->_ref_product = new CProduct;
    $this->_ref_product->load($this->product_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_product) {
      $this->loadRefsFwd();
    }
    return $this->_ref_product->getPerm($permType);
  }
}
?>