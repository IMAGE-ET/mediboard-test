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
  var $reception_id       = null;
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
    $specs['reception_id']  = 'ref notNull class|CProductReception';
    $specs['quantity']      = 'num notNull';
    $specs['code']          = 'str';
    $specs['lapsing_date']  = 'date mask|99/99/9999 format|$3-$2-$1';
    $specs['date']          = 'dateTime notNull';
    $specs['barcode_printed'] = 'bool';
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['lines_dmi']  = 'CPrescriptionLineDMI order_item_reception_id';
    $backProps['bill_items'] = 'CProductReceptionBillItem reception_item_id';
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefOrderItem();
    $this->_view = "$this->quantity x $this->_ref_order_item";
  }
  
  function loadRefOrderItem() {
    $this->_ref_order_item = $this->loadFwdRef("order_item_id", true);
  }
  
  function loadRefReception() {
    $this->_ref_reception = $this->loadFwdRef("reception_id", true);
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefOrderItem();
    $this->loadRefReception();
  }
  
  function delete(){
    $this->loadRefOrderItem();
    $this->_ref_order_item->loadReference();
    $this->_ref_order_item->_ref_reference->loadRefProduct();
    $product = $this->_ref_order_item->_ref_reference->_ref_product;
    if ($product->loadRefStock()) {
      $this->completeField("quantity");
      $product->_ref_stock_group->quantity -= $this->quantity;
      $product->_ref_stock_group->store();
    }
    return parent::delete();
  }
  
  function getQuantity(){
    $this->loadRefOrderItem();
    $item = $this->_ref_order_item;
    $item->loadReference();
    $reference = $item->_ref_reference;
    $item->_ref_reference->loadRefProduct();
    return $item->quantity * $reference->quantity * $item->_ref_reference->_ref_product->quantity;
  }

  function store() {
    $this->completeField("reception_id");
    
    if (!$this->_id && !$this->reception_id) {
      $this->loadRefOrderItem();
      $this->_ref_order_item->loadOrder();
      
      $reception = new CProductReception;
      $reception->date = mbDateTime();
      $reception->societe_id = $this->_ref_order_item->_ref_order->societe_id;
      $reception->group_id = CGroups::loadCurrent()->_id;
      if ($msg = $reception->store()) {
        return $msg;
      }
      
      $this->reception_id = $reception->_id;
    }
    
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
      global $g;
      $qty = $this->quantity * $product->_unit_quantity;
      $stock = new CProductStockGroup();
      $stock->product_id = $product->_id;
      $stock->group_id = $g;
      $stock->quantity = $qty;
      $stock->order_threshold_min = $qty;
      
      CAppUI::setMsg("Un nouveau stock a יtי crיי", UI_MSG_OK);
      //CAppUI::setMsg("Un nouveau stock pour [%s] a יtי crיי", UI_MSG_OK, $product->_view);
    }
    if ($msg = $stock->store()) {
      return $msg;
    }
    
    return parent::store();
  }
}
?>