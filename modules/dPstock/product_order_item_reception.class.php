<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPtock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

class CProductOrderItemReception extends CMbObject {
  // DB Table key
  var $order_item_reception_id = null;

  // DB Fields
  var $order_item_id      = null;
  var $quantity           = null;
  var $code               = null;
  var $date               = null;

  // Object References
  //    Single
  var $_ref_order_item    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order_item_reception';
    $spec->key   = 'order_item_reception_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['order_item_id'] = 'notNull ref class|CProductOrderItem';
    $specs['quantity']      = 'notNull num';
    $specs['code']          = 'str';
    $specs['date']          = 'notNull dateTime';
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    /*$this->loadRefOrderItem();
    $this->_ref_order_item->updateFormFields();*/
    $this->_view = $this->quantity;//$this->_ref_order_item->_view." ($this->quantity)";
  }
  
  function loadRefOrderItem() {
    $this->_ref_order_item = new CProductOrderItem();
    $this->_ref_order_item->load($this->order_item_id);
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefOrderItem();
  }

  function store() {
    $this->loadRefOrderItem();
    $this->_ref_order_item->loadRefsFwd();
    $this->_ref_order_item->_ref_reference->loadRefsFwd();
    $this->_ref_order_item->_ref_reference->_ref_product->loadRefStock();
    
    $stock = $this->_ref_order_item->_ref_reference->_ref_product->_ref_stock_group;
    
    if ($stock)
      $stock->quantity += $this->quantity;
    else {
      global $AppUI;
      $stock = new CProductStockGroup();
      $stock->product_id = $this->_ref_order_item->_ref_reference->product_id;
      $stock->quantity = $this->quantity;
      $stock->order_threshold_min = 1;
      $stock->order_threshold_max = 1;
      $AppUI->setMsg('Un nouveau stock pour ['.$this->_ref_order_item->_ref_reference->_ref_product->_view.'] a été créé', UI_MSG_INFO);
    }
    if ($msg = $stock->store()) {
      return $msg;
    }
    
    return parent::store();
  }
}
?>