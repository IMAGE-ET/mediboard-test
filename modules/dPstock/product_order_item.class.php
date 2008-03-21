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
  var $_receive           = null;

  function CProductOrderItem() {
    $this->CMbObject('product_order_item', 'order_item_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      'reference_id'      => 'notNull ref class|CProductReference',
      'order_id'          => 'notNull ref class|CProductOrder',
      'quantity'          => 'notNull num pos',
      'unit_price'        => 'currency',
      'date_received'     => 'dateTime',
      'quantity_received' => 'num pos',
	    '_price'            => 'currency',
      '_receive'          => 'bool',
    );
  }

  function receive($count) {
    $this->load();
    $this->loadRefsFwd();
    
    // Quantity of this article not received yet
    $quantity_not_received = ($this->quantity - $this->quantity_received);
    
    // If we want to receive more of article than the total quantity, we set it to the max
    if ($this->_receive > $quantity_not_received) {
    	$this->_receive = $quantity_not_received;
    }
    
    // If we want to un-receive more articles than the already received quantity, we set it to -(quantity received)
    if ($this->_receive < 0 && abs($this->_receive) > $this->quantity_received) {
      $this->_receive = -$this->quantity_received;
    }
    
    // The stock relative to this order item is loaded
    $stock = new CProductStock();
    $where = array();
    $where['group_id']   = "= '{$this->_ref_order->group_id}'";
    $where['product_id'] = "= '{$this->_ref_reference->product_id}'";

    // The quantity of the article that has been received is added to the stock
    if ($stock->loadObject($where)) {
      $stock->quantity += $this->_receive * $this->_ref_reference->quantity;
      $stock->store();
    }
    
    // The order item is updated
    $this->date_received = mbDateTime();
    $this->quantity_received += $this->_receive;
    $this->store();
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);
    $this->_view = $this->_ref_reference->_view;
    $this->_price = $this->unit_price * $this->quantity;
  }
  
  function updateDBFields() {
  	if ($this->_receive) {
      $this->receive($this->_receive);
  	}
  }

  function loadRefsFwd() {
    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);

    $this->_ref_order = new CProductOrder();
    $this->_ref_order->load($this->order_id);
  }

  function store() {
    if($this->order_id && $this->reference_id && !$this->_id) {
      $where['order_id']     = "= '$this->order_id'";
      $where['reference_id'] = "= '$this->reference_id'";

      $duplicateKey = new CProductOrderItem();
      $this->loadRefsFwd();
      
      if($duplicateKey->loadObject($where)) {
        $duplicateKey->loadRefsFwd();
        $this->_id = $duplicateKey->_id;
        $this->quantity += $duplicateKey->quantity;
        $this->unit_price = $duplicateKey->unit_price;
        $this->date_received = $duplicateKey->date_received;
      } else {
        $this->unit_price = $this->_ref_reference->price;
      }
    }
    return parent::store();
  }
}
?>