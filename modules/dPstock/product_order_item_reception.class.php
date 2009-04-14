<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductOrderItemReception extends CMbObject {
  // DB Table key
  var $order_item_reception_id = null;

  // DB Fields
  var $order_item_id      = null;
  var $quantity           = null;
  var $code               = null;
  var $lapsing_date       = null;
  var $date               = null;
  var $barcode_printed    = null;

  // Object References
  //    Single
  var $_ref_order_item    = null;
  
  var $_cancel            = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order_item_reception';
    $spec->key   = 'order_item_reception_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['order_item_id'] = 'ref notNull class|CProductOrderItem';
    $specs['quantity']      = 'num notNull';
    $specs['code']          = 'str';
    $specs['lapsing_date']  = 'date mask|99/99/9999 format|$3-$2-$1';
    $specs['date']          = 'dateTime notNull';
    $specs['barcode_printed'] = 'bool';
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['lines_dmi'] = 'CPrescriptionLineDMI order_item_reception_id';
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefOrderItem();
    /*$this->_ref_order_item->updateFormFields();*/
    $this->_view = $this->quantity.' x '.$this->_ref_order_item->_view;
  }
  
  function loadRefOrderItem() {
    $this->_ref_order_item = new CProductOrderItem();
    $this->_ref_order_item = $this->_ref_order_item->getCached($this->order_item_id);
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
    
    $product = &$this->_ref_order_item->_ref_reference->_ref_product;
    $product->updateFormFields();

    if ($product->loadRefStock()) {
    	$stock = $product->_ref_stock_group;
      $stock->quantity += $this->quantity * $product->_unit_quantity;
    }
    else {
      global $AppUI, $g;
      $qty = $this->quantity * $product->_unit_quantity;
      $stock = new CProductStockGroup();
      $stock->product_id = $product->_id;
      $stock->group_id = $g;
      $stock->quantity = $qty;
      $stock->order_threshold_min = $qty;
      $stock->order_threshold_max = $qty * 2;
      $AppUI->setMsg('Un nouveau stock pour ['.$product->_view.'] a יtי crיי', UI_MSG_OK);
    }
    if ($msg = $stock->store()) {
      return $msg;
    }
    
    if ($this->_cancel && $this->_id) {
    	$product->_ref_stock_group->quantity -= $this->quantity;
    	$product->_ref_stock_group->store();
    	$this->delete();
    	$this->_cancel = null;
    	return;
    }
    
    return parent::store();
  }
}
?>