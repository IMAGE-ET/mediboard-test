<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CProductOrderItemReception extends CMbObject {
  // DB Table key
  public $order_item_reception_id;

  // DB Fields
  public $order_item_id;
  public $reception_id;
  public $quantity;
  public $code;
  public $serial;
  public $lapsing_date;
  public $date;
  public $barcode_printed;
  public $cancelled;

  // Object References
  //    Single
  /**
   * @var CProductOrderItem
   */
  public $_ref_order_item;
  /**
   * @var CProductReception
   */
  public $_ref_reception;
  
  public $_cancel;
  public $_price;
  
  // #TEMP#
  public $units_fixed;
  public $orig_quantity;
  
  static $_load_lite = false;

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
    $specs['serial']        = 'str maxLength|40';
    $specs['lapsing_date']  = 'date mask|99/99/9999 format|$3-$2-$1';
    $specs['date']          = 'dateTime notNull';
    $specs['barcode_printed'] = 'bool';
    $specs['cancelled']     = 'bool notNull default|0';
    $specs['_price']        = 'currency';
    
    // #TEMP#
    $specs['units_fixed']   = 'bool show|0';
    $specs['orig_quantity'] = 'num show|0';
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['lines_dmi']  = 'CPrescriptionLineDMI order_item_reception_id';
    $backProps['bill_items'] = 'CProductReceptionBillItem reception_item_id';
    $backProps['order_items']= 'CProductOrderItem lot_id';
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

  /**
   * @return CProductOrderItem
   */
  function loadRefOrderItem() {
    return $this->_ref_order_item = $this->loadFwdRef("order_item_id", true);
  }

  /**
   * @return CProductReception
   */
  function loadRefReception() {
    return $this->_ref_reception = $this->loadFwdRef("reception_id", true);
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    
    if (self::$_load_lite) {
      return;
    }
    
    $this->loadRefOrderItem();
    $this->loadRefReception();
  }
  
  function delete(){
    $this->completeField("order_item_id", "quantity");
    
    $this->loadRefOrderItem();
    $item = $this->_ref_order_item;
    
    $item->loadReference();
    $reference = $item->_ref_reference;
    
    $reference->loadRefProduct();
    $product = $reference->_ref_product;
    
    if ($product->loadRefStock()) {
      $product->_ref_stock_group->quantity -= $this->quantity;
    }
    
    // If the order is already flagged as received, 
    // we check if it will still be after deletion
    $item->loadOrder();
    $order = $item->_ref_order;
    
    if ($order->_id && $order->received) {
      $count_renewed = $order->countRenewedItems();
      $count_received = $order->countReceivedItems() - (count($order->_ref_order_items) - $count_renewed);
      
      if ($count_received < $count_renewed) {
        $order->received = 0;
      }
    }
    
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    // we store other objects only if deletion was ok !
    if ($product->_ref_stock_group && $product->_ref_stock_group->_id) {
      $product->_ref_stock_group->store();
    }
    
    if ($order && $order->_id) {
      $order->store();
    }
  }
 
  function getUsedQuantity(){
    $query = "SELECT SUM(prescription_line_dmi.quantity) 
              FROM prescription_line_dmi
              WHERE prescription_line_dmi.order_item_reception_id = $this->_id";
    $ds = $this->_spec->ds;
    $row = $ds->fetchRow($ds->query($query));
    return intval(reset($row));
  }
  
  function store() {
    $this->completeField("reception_id");
    
    $is_new = !$this->_id;
    
    if ($is_new && $this->cancelled === null) {
      $this->cancelled = 0;
    }
    
    if ($is_new) {
      $this->loadRefOrderItem();
      $this->_ref_order_item->loadOrder();
    }
      
    if ($is_new && !$this->reception_id) {
      $order = $this->_ref_order_item->_ref_order;
      $reception = new CProductReception;
      $reception->date = CMbDT::dateTime();
      $reception->societe_id = $order->societe_id;
      $reception->group_id = CProductStockGroup::getHostGroup();
      
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

    if ($is_new) {
      $this->_ref_order_item->loadRefsFwd();
      $this->_ref_order_item->_ref_reference->loadRefsFwd();
      $this->_ref_order_item->_ref_reference->_ref_product->loadRefStock();
      
      $product = &$this->_ref_order_item->_ref_reference->_ref_product;
      $product->updateFormFields();
    
      if ($product->loadRefStock()) {
        $stock = $product->_ref_stock_group;
        $stock->quantity += $this->quantity;
      }
      else {
        $qty = $this->quantity;
        $stock = new CProductStockGroup();
        $stock->product_id = $product->_id;
        $stock->group_id = CProductStockGroup::getHostGroup();
        $stock->quantity = $qty;
        $stock->order_threshold_min = $qty;
        
        CAppUI::setMsg("Un nouveau stock a été créé", UI_MSG_OK);
        //CAppUI::setMsg("Un nouveau stock pour [%s] a été créé", UI_MSG_OK, $product->_view);
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
      if (!$order->received) {
        $count_renewed = $order->countRenewedItems();
        $count_received = $order->countReceivedItems() - (count($order->_ref_order_items) - $count_renewed);
        
        if ($count_renewed && ($count_received >= $count_renewed)) {
          $order->received = 1;
          $order->store();
        }
      }
    }
  }
}
