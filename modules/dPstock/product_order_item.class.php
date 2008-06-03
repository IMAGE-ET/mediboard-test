<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPtock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductOrderItem extends CMbObject {
  // DB Table key
  var $order_item_id      = null;

  // DB Fields
  var $reference_id       = null;
  var $order_id           = null;
  var $quantity           = null;
  var $unit_price         = null; // In the case the reference price changes
  var $date_received      = null;
  var $quantity_received  = null;

  // Object References
  //    Single
  var $_ref_order         = null;
  var $_ref_reference     = null;

  // Form fields
  var $_price             = null;
  var $_quantity_received  = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order_item';
    $spec->key   = 'order_item_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'reference_id'      => 'notNull ref class|CProductReference',
      'order_id'          => 'notNull ref class|CProductOrder',
      'quantity'          => 'notNull num pos',
      'unit_price'        => 'currency',
      'date_received'     => 'dateTime',
      'quantity_received' => 'num',
	    '_price'            => 'currency',
      '_quantity_received'=> 'num',
    ));
  }

  function receive() {
  	$this->load();
    $this->loadRefsFwd();
    
    // if a bad quantity received is given, we set it to a normal quantity
    if ($this->_quantity_received > $this->quantity) {
      $this->_quantity_received = $this->quantity;
    } else if ($this->_quantity_received < 0) {
      $this->_quantity_received = 0;
    }
    
    // the difference between before and after
    $delta = $this->_quantity_received - $this->quantity_received;
    
    // the new quantity is saved
    $this->quantity_received = $this->_quantity_received;
    
    // the new reception date is saved
    if ($this->quantity_received != 0 && $delta > 0) {
      $this->date_received = mbDateTime();
    }
    
    // The quantity of this article that has been received is added to the stock
    if ($stock = $this->getStock()) {
      $stock->quantity += $delta * $this->_ref_reference->quantity;
      $stock->store();
    }
    
    if ($this->quantity_received == 0) {
      $this->date_received = null;
    }
  }
  
  function isReceived() {
  	return ($this->date_received && ($this->quantity == $this->quantity_received));
  }
  
  function getStock() {
    if (!$this->_ref_order || $this->_ref_reference) {
      $this->loadRefsFwd();
    }
    $stock = new CProductStock();
    $where = array();
    $where['group_id']   = "= '{$this->_ref_order->group_id}'";
    $where['product_id'] = "= '{$this->_ref_reference->product_id}'";
    $stock->loadObject($where);
    
    return $stock;
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);
    $this->_view = $this->_ref_reference->_view;
    $this->_price = $this->unit_price * $this->quantity;
  }
  
  function updateDBFields() {
  	if (!is_null($this->_quantity_received)) {
      $this->receive();
  	}
  }

  function loadRefsFwd() {
    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);

    $this->_ref_order = new CProductOrder();
    $this->_ref_order->load($this->order_id);
  }

  function store() {
    global $AppUI;
    
    $this->loadRefsFwd();
    
    if($this->order_id && $this->reference_id && !$this->_id) {
      $where['order_id']     = "= '$this->order_id'";
      $where['reference_id'] = "= '$this->reference_id'";

      $duplicateKey = new CProductOrderItem();
      
      if($duplicateKey->loadObject($where)) {
        $duplicateKey->loadRefsFwd();
        $this->_id = $duplicateKey->_id;
        $this->quantity += $duplicateKey->quantity;
        $this->unit_price = $duplicateKey->unit_price;
      } else {
        $this->unit_price = $this->_ref_reference->price;
      }
    }
    
    if ($stock = $this->getStock()) {
      $stock->loadRefOrders();
      if ($stock->_zone_future > 2) {
        $AppUI->setMsg('Attention : le stock optimum risque d\'être dépassé', UI_MSG_WARNING);
      }
    }
    
    return parent::store();
  }
}
?>