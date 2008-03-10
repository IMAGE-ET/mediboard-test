<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductStockOut extends CMbObject {
  // DB Table key
  var $stock_out_id  = null;

  // DB Fields
  var $stock_id      = null;
  var $date          = null;
  var $quantity      = null;
  var $product_code  = null; // Lot number, lapsing date
  var $function_id   = null;

  // Object References
  //    Single
  var $_ref_stock    = null;
  var $_ref_function = null;

  function CProductStock() {
    $this->CMbObject('product_stock_out', 'stock_out_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      'stock_id'     => 'notNull ref class|CProductStock',
      'date'         => 'notNull dateTime',
      'quantity'     => 'num pos',
      'product_code' => 'num pos',
      'function_id'  => 'ref class|CFunctions',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_stock->_view.($this->function_id?" (pour le service <i>{$this->_ref_function->_view}</i>)":'');
  }

  function loadRefsFwd(){
    $this->_ref_stock = new CProductStock();
    $this->_ref_stock->load($this->stock_id);

    if ($this->function_id) {
      $this->_ref_function = new CFunctions();
      $this->_ref_function->load($this->function_id);
    }
  }

  function getPerm($permType) {
    if(!$this->_ref_stock || !$this->_ref_function) {
      $this->loadRefsFwd();
    }
    if ($this->_ref_function) {
      return ($this->_ref_stock->getPerm($permType) && $this->_ref_function->getPerm($permType));
    } else {
      return ($this->_ref_stock->getPerm($permType));
    }
  }
}
?>