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
  /**
   * @var CProductOrderItem
   */
  var $_ref_order_item    = null;
  /**
   * @var CProductReception
   */
  var $_ref_reception     = null;
  
  var $_cancel            = null;
  var $_price             = null;
  var $_unit_quantity     = null;

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
    $specs['code']          = 'str seekable';
    $specs['lapsing_date']  = 'date mask|99/99/9999 format|$3-$2-$1';
    $specs['date']          = 'dateTime notNull';
    $specs['barcode_printed'] = 'bool';
    $specs['_price']        = 'currency';
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['lines_dmi']  = 'CPrescriptionLineDMI order_item_reception_id';
    $backProps['bill_items'] = 'CProductReceptionBillItem reception_item_id';
    $backProps['lots']       = 'CProductOrderItem lot_id';
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->quantity;
    if ($this->code) {
      $this->_view .= " [$this->code]";
    }
  }
  
  function computePrice(){
    $this->loadRefOrderItem();
    return $this->_price = $this->quantity * $this->_ref_order_item->unit_price;
  }
  
  function loadRefOrderItem() {
    return $this->_ref_order_item = $this->loadFwdRef("order_item_id", true);
  }
  
  function loadRefReception() {
    return $this->_ref_reception = $this->loadFwdRef("reception_id", true);
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefOrderItem();
    $this->loadRefReception();
  }
  
  function delete(){
    $this->loadRefOrderItem();
    $item = $this->_ref_order_item;
    
    $item->loadReference();
    $reference = $item->_ref_reference;
    
    $reference->loadRefProduct();
    $product = $reference->_ref_product;
    
    if ($product->loadRefStock()) {
      $product->_ref_stock_group->quantity -= $this->getUnitQuantity();
      $product->_ref_stock_group->store();
    }
    return parent::delete();
  }
  
  function getUnitQuantity(){
    $this->completeField("quantity");
    
    $this->loadRefOrderItem();
    $item = $this->_ref_order_item;
    
    $item->loadReference();
    $reference = $item->_ref_reference;
    
    return $this->_unit_quantity = $this->quantity * $reference->_unit_quantity;
  }

  function store() {
    $this->completeField("reception_id");
    
    $is_new = !$this->_id;
    
    $this->loadRefOrderItem();
    $this->_ref_order_item->loadOrder();
      
    if ($is_new && !$this->reception_id) {
      $order = $this->_ref_order_item->_ref_order;
      $reception = new CProductReception;
      $reception->date = mbDateTime();
      $reception->societe_id = $order->societe_id;
      $reception->group_id = CGroups::loadCurrent()->_id;
      
      // Recherche de receptions ayant un numero de reception similaire pour gerer l'increment
      if ($order->order_number) {
        $where = array("reference" => "LIKE '{$order->order_number}%'");
        $number = $reception->countList($where) + 1;
        $reception->reference = "{$order->order_number}-$number";
      }
      
      if ($msg = $reception->store()) {
        return $msg;
      }
      
      $this->reception_id = $reception->_id;
    }
    
    $this->_ref_order_item->loadRefsFwd();
    $this->_ref_order_item->_ref_reference->loadRefsFwd();
    $this->_ref_order_item->_ref_reference->_ref_product->loadRefStock();
    
    $product = &$this->_ref_order_item->_ref_reference->_ref_product;
    $product->updateFormFields();

    if ($is_new) {
      if ($product->loadRefStock()) {
      	$stock = $product->_ref_stock_group;
        $stock->quantity += $this->getUnitQuantity();
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
    }
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    // If the order is received, we set the flag
    if ($is_new) {
      $order = $this->_ref_order_item->_ref_order;
      $count_received = $order->countReceivedItems();
      $count_renewed = $order->countRenewedItems();
      
      if ($count_renewed && !$order->received && ($count_received >= $count_renewed)) {
        $order->received = 1;
        $order->store();
      }
    }
  }
}
?>