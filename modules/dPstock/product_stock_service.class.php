<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

class CProductStockService extends CProductStock {
  // DB Fields
  var $function_id   = null;

  // Object References
  //    Single
  var $_ref_function = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_service';
    $spec->key   = 'stock_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['function_id'] = 'notNull ref class|CFunctions';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view." ({$this->_ref_function->_view})";
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return parent::getPerm($permType) && $this->_ref_function->getPerm($permType);
  }
}
?>