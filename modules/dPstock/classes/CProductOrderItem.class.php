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

class CProductOrderItem extends CMbObject {
  // DB Table key
  var $order_item_id      = null;

  // DB Fields
  var $reference_id       = null;
  var $order_id           = null;
  var $quantity           = null;
  var $unit_price         = null; // In the case the reference price changes
  var $tva                = null; // In the case the reference tva changes
  var $lot_id             = null;
  var $renewal            = null;
  var $septic             = null;

  // Object References
  //    Single
  /**
   * @var CProductOrder
   */
  var $_ref_order         = null;
  /**
   * @var CProductReference
   */
  var $_ref_reference     = null;
  var $_ref_lot           = null;
  var $_ref_stock_group   = null;
  
  //    Multiple
  var $_ref_receptions    = null;

  // Form fields
  var $_price        = null;
  var $_date_received     = null;
  var $_quantity_received = null;
  var $_id_septic         = null;
  var $_update_reference  = null;
  
  // #TEMP#
  var $units_fixed        = null;
  var $orig_quantity      = null;
  var $orig_unit_price    = null;
  
  static $_load_lite = false;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order_item';
    $spec->key   = 'order_item_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['reference_id']       = 'ref notNull class|CProductReference';
    $specs['order_id']           = 'ref class|CProductOrder'; // can be null because of gifts
    $specs['quantity']           = 'num notNull pos';
    $specs['unit_price']         = 'currency precise';
    $specs['tva']                = 'pct';
    $specs['lot_id']             = 'ref class|CProductOrderItemReception';
    $specs['renewal']            = 'bool notNull default|1';
    $specs['septic']             = 'bool notNull default|0';
    $specs['_price']             = 'currency';
    $specs['_quantity_received'] = 'num';
    $specs['_update_reference']  = 'bool';
    
    // #TEMP#
    $specs['units_fixed']        = 'bool show|0';
    $specs['orig_quantity']      = 'num show|0';
    $specs['orig_unit_price']    = 'currency precise show|0';
    return $specs;
  }

  function receive($quantity, $code = null) {
    if ($this->_id) {
      $reception = new CProductOrderItemReception();
      $reception->order_item_id = $this->_id;
      $reception->quantity = $quantity;
      $reception->date = mbDateTime();
      $reception->code = $code;
      return $reception->store();
    }
    else {
      return "$this->_class::receive failed : order_item must be stored before";
    }
  }
  
  function isReceived() {
    $this->completeField("renewal");
    
    if ($this->renewal == 0) {
      return true;
    }
    
    $this->updateReceived();
    return $this->_quantity_received >= $this->quantity;
  }
  
  function getStock() {
    if ($this->_ref_stock_group) {
      return $this->_ref_stock_group;
    }
    
    $this->loadReference();
    $this->loadOrder();
    
    $stock = new CProductStockGroup();
    $stock->group_id = $this->_ref_order->group_id;
    $stock->product_id = $this->_ref_reference->product_id;
    $stock->loadMatchingObject();
    
    return $this->_ref_stock_group = $stock;
  }
  
  function updateReceived() {
    $this->loadRefsReceptions();
    
    $quantity = 0;
    foreach ($this->_ref_receptions as $reception) {
      $quantity += $reception->quantity;
    }
    $this->_quantity_received = $quantity;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    if (self::$_load_lite) {
      return;
    }
    
    $this->updateReceived();
    $this->loadReference();
    
    $this->_view = $this->_ref_reference->_view;
    $this->_price = $this->unit_price * $this->quantity;
  }
  
  function loadReference() {
    return $this->_ref_reference = $this->loadFwdRef("reference_id", true);
  }
  
  function loadRefLot() {
    return $this->_ref_lot = $this->loadFwdRef("lot_id", false);
  }
  
  function loadOrder($cache = true) {
    $this->completeField("order_id");
    return $this->_ref_order = $this->loadFwdRef("order_id", $cache);
  }  
  
  function loadRefsReceptions() {
    return $this->_ref_receptions = $this->loadBackRefs('receptions', 'date DESC');
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    
    if (self::$_load_lite) {
      return;
    }

    $this->loadReference();
    $this->loadOrder();
    $this->loadRefLot();
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['receptions'] = 'CProductOrderItemReception order_item_id';
    return $backProps;
  }
  
  function loadRefsBack() {
    $this->loadRefsReceptions();
  }

  function store() {
    $this->completeField("order_id", "reference_id", "renewal", "septic");
    
    if (!$this->_id) {
      if ($this->renewal === null) $this->renewal = "1";
      if ($this->septic  === null) $this->septic  = "0";
    }
    
    if ($this->order_id && $this->reference_id && !$this->_id) {
      $this->loadRefsFwd();
      
      $where = array(
        'order_id'     => "= '$this->order_id'",
        'reference_id' => "= '$this->reference_id'",
        'renewal' => "= '$this->renewal'",
        'septic' => "= '$this->septic'",
      );
      
      if ($this->lot_id) {
        $where['lot_id'] = "= '$this->lot_id'";
      }
      
      $duplicateKey = new CProductOrderItem();
      if ($duplicateKey->loadObject($where)) {
        $duplicateKey->loadRefsFwd();
        $this->_id = $duplicateKey->_id;
        $this->quantity += $duplicateKey->quantity;
        $this->unit_price = $duplicateKey->unit_price;
        $this->tva = $duplicateKey->tva;
      }
      else {
        $this->unit_price = $this->_ref_reference->price;
        $this->tva = $this->_ref_reference->tva;
      }
    }
    
    if ($this->_id && $this->_update_reference) {
      $ref = $this->loadReference();
      $ref->price = $this->unit_price;
      if ($msg = $ref->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
      else {
        CAppUI::setMsg('Prix de la référence mis à jour', UI_MSG_OK);
      }
      $this->_update_reference = null;
    }
    
    /*if (!$this->_id && ($stock = $this->getStock())) {
      $stock->loadRefOrders();
      if ($stock->_zone_future > 2) {
        CAppUI::setMsg("Attention : le stock optimum risque d'être dépassé", UI_MSG_WARNING);
      }
    }*/
    
    return parent::store();
  }
  
  function delete(){
    $order = $this->loadOrder(false);
    
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    if ($order->countBackRefs("order_items") == 0) {
      return $order->delete();
    }
  }
}
?>