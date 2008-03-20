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

  function receive() {
    if (!$this->date_received) {
    	$this->load();
      $this->loadRefsFwd();
      
      $this->quantity_received = (($this->quantity_received > 0) && ($this->quantity_received <= $this->quantity)) 
                                 ? $this->quantity_received 
                                 : $this->quantity;
      
      $stock = new CProductStock();
      $where = array();
      $where['group_id']   = "= '{$this->_ref_order->group_id}'";
      $where['product_id'] = "= '{$this->_ref_reference->product_id}'";
      
      $this->date_received = mbDateTime();

      if ($stock->loadObject($where)) {
        $stock->quantity += $this->quantity_received * $this->_ref_reference->quantity; //FIXME : 
        $stock->store();
      }
      
      $this->store();
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);
    $this->_view = $this->_ref_reference->_view;
    $this->_price = $this->unit_price * $this->quantity;
  }
  
  function updateDBFields() {
    if ($this->_receive == 1) {
    	$this->receive();
    } else if ($this->_receive == -1) {
    	
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