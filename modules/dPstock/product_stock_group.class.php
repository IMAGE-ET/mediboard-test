<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

class CProductStockGroup extends CProductStock {
  // DB Fields
  var $group_id                 = null;

  // Object References
  //    Single
  var $_ref_group               = null;

  //    Multiple
  var $_ref_deliveries          = null;
  
  var $_zone_future             = null;
  var $_ordered_count           = null;
  var $_ordered_last            = null;
  var $_orders                  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_group';
    $spec->key   = 'stock_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['deliveries'] = 'CProductDelivery stock_id';
    return $backRefs;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['group_id']       = 'notNull ref class|CGroups';
    $specs['_ordered_count'] = 'num pos';
    $specs['_ordered_last']  = 'dateTime';
    $specs['_zone_future']   = 'num';
    return $specs;
  }
  
  function loadRefOrders() {
    // Verifies wether there are pending orders for this stock
    $where = array();
    $where['date_ordered'] = 'IS NOT NULL';
    $where[] = 'deleted IS NULL OR deleted = 0';
    $orderby = 'date_ordered ASC';
    $order = new CProductOrder();

    $list_orders = $order->loadList($where, $orderby);
    $this->_orders = array();
    
    foreach ($list_orders as $order) {
      $order->updateFormFields();
      if (!$order->_received && !$order->cancelled) {
        $done = false;
        foreach ($order->_ref_order_items as $item) {
          $item->loadRefsFwd();
          $item->_ref_reference->loadRefsFwd();
          $item->_ref_order->loadRefsFwd();
          
          if ($item->_ref_reference->_ref_product && $this->_ref_product && $item->_ref_reference->_ref_product->_id == $this->_ref_product->_id) {
            $this->_ordered_count += $item->quantity * $item->_ref_reference->quantity;
            $this->_ordered_last = max(array($item->_ref_order->date_ordered, $this->_ordered_last));
            if (!$done) {
              $this->_orders[] = $order;
              $done = true;
            }
          }
        }
      }
    }
    
    $future_quantity = $this->quantity + $this->_ordered_count;
    
    if ($future_quantity <= $this->order_threshold_critical) {
      $this->_zone_future = 0;
      
    } elseif ($future_quantity <= $this->order_threshold_min) {
      $this->_zone_future = 1;
      
    } elseif ($future_quantity <= $this->order_threshold_optimum) {
      $this->_zone_future = 2;
      
    } else {
      $this->_zone_future = 3;
    }
  }
  
  static function getFromCode($code) {
    $stock = new CProductStockGroup();
    
    $where = array();
    $where['product.code'] = "= '$code'";
    $ljoin = array();
    $ljoin['product'] = 'product_stock_group.product_id = product.product_id';

    $stock->loadObject($where, null, null, $ljoin);
    return $stock;
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->_ref_group = new CGroups;
    $this->_ref_group = $this->_ref_group->getCached($this->group_id);
  }

  function loadRefsBack(){
    $this->_ref_deliveries = $this->loadBackRefs('deliveries');
  }
  
  function getPerm($permType) {
    if(!$this->_ref_group) {
      $this->loadRefsFwd();
    }
    return parent::getPerm($permType) && $this->_ref_group->getPerm($permType);
  }

  function check() {
    if($this->product_id && $this->group_id) {
      $where['product_id'] = "= '$this->product_id'";
      $where['group_id']   = "= '$this->group_id'";
      $where['stock_id']   = " != '$this->stock_id'";
      
      $VerifDuplicateKey = new CProductStockGroup();
      $ListVerifDuplicateKey = $VerifDuplicateKey->loadList($where);
      
      if(count($ListVerifDuplicateKey) != 0) {
        return 'Erreur : Le stock de ce produit existe déjà';
      }
    }
    
    return parent::check();
  }
}
?>