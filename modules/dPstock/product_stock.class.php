<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductStock extends CMbObject {
  // DB Table key
  var $stock_id                 = null;

  // DB Fields
  var $product_id               = null;
  var $group_id                 = null;
  var $quantity                 = null;
  var $order_threshold_critical = null;
  var $order_threshold_min      = null;
  var $order_threshold_optimum  = null;
  var $order_threshold_max      = null;

  // Object References
  //    Single
  var $_ref_product             = null;
  var $_ref_group               = null;

  //    Multiple
  var $_ref_stock_outs          = null;
  
  // Stock percentages 
  var $_quantity                = null;
  var $_critical                = null;
  var $_min                     = null;
  var $_optmimum                = null;
  var $_max                     = null;
  // In which part of the graph the quantity is
  var $_zone                    = null;
  var $_zone_future             = null;
  
  var $_ordered_count           = null;
  var $_ordered_last            = null;
  var $_orders                  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock';
    $spec->key   = 'stock_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['stock_outs'] = 'CProductStockOut stock_id';
    return $backRefs;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'product_id'               => 'notNull ref class|CProduct',
      'group_id'                 => 'notNull ref class|CGroups',
      'quantity'                 => 'num notNull',
      'order_threshold_critical' => 'num',
      'order_threshold_min'      => 'num pos notNull moreEquals|order_threshold_critical',
      'order_threshold_optimum'  => 'num pos moreEquals|order_threshold_min',
      'order_threshold_max'      => 'num pos notNull moreEquals|order_threshold_optimum',
      '_quantity'                => 'pct',
      '_critical'                => 'pct',
      '_min'                     => 'pct',
      '_optimum'                 => 'pct',
      '_max'                     => 'pct',
      '_zone'                    => 'num',
      '_zone_future'             => 'num',
	    '_ordered_count'           => 'num pos',
	    '_ordered_last'            => 'dateTime',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view;
    
    // Calculation of the levels for the bargraph
    $max = max(array($this->quantity, $this->order_threshold_max)) / 100;
    if ($max > 0) {
	    $this->_quantity = $this->quantity                 / $max;
	    $this->_critical = $this->order_threshold_critical / $max;
	    $this->_min      = $this->order_threshold_min      / $max - $this->_critical;
	    $this->_optimum  = $this->order_threshold_optimum  / $max - $this->_critical - $this->_min;
	    $this->_max      = $this->order_threshold_max      / $max - $this->_critical - $this->_min - $this->_optimum;
	      
	    if ($this->quantity <= $this->order_threshold_critical) {
	      $this->_zone = 0;
	      
	    } elseif ($this->quantity <= $this->order_threshold_min) {
	      $this->_zone = 1;
	      
	    } elseif ($this->quantity <= $this->order_threshold_optimum) {
	      $this->_zone = 2;
	      
	    } else {
	      $this->_zone = 3;
	    }
	  }
  }
  
  function loadRefOrders() {
    // Verifies wether there are pending orders for this stock
    $where = array();
    $where['date_ordered'] = 'IS NOT NULL';
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
  
  /*function updateDBFields() {
    if (!$this->order_threshold_critical) {
    	$this->order_threshold_critical = $this->order_threshold_min;
    }
    if (!$this->order_threshold_optimum) {
      $this->order_threshold_optimum = round(($this->order_threshold_min+$this->order_threshold_max)/2);
    }
    //mbTrace($this);
  }*/

  function loadRefsFwd(){
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);

    $this->_ref_product = new CProduct;
    $this->_ref_product->load($this->product_id);
  }

  function loadRefsBack(){
    $this->_ref_stock_outs = $this->loadBackRefs('stock_outs');
  }
  
  function getPerm($permType) {
    if(!$this->_ref_group || !$this->_ref_product) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_group->getPerm($permType) && $this->_ref_product->getPerm($permType));
  }

  function check() {
    if($this->product_id && $this->group_id) {
      $where['product_id'] = "= '$this->product_id'";
      $where['group_id']   = "= '$this->group_id'";
      $where['stock_id']   = " != '$this->stock_id'";
      
      $VerifDuplicateKey = new CProductStock();
      $ListVerifDuplicateKey = $VerifDuplicateKey->loadList($where);
      
      if(count($ListVerifDuplicateKey) != 0) {
        return 'Erreur : Le stock de ce produit existe déjà';
      }
    }
    
    return parent::check();
  }
}
?>