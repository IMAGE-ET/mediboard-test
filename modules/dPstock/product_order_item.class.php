<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPtock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductOrderItem extends CMbObject {
  // DB Table key
  var $order_item_id  = null;

  // DB Fields
  var $reference_id   = null;
  var $order_id       = null;
  var $quantity       = null;
  var $unit_price     = null; // In the case the reference price changes
  var $date_received  = null;

  // Object References
  //    Single
  var $_ref_order     = null;
  var $_ref_reference = null;

  // Form fields
  var $_price         = null;
  var $_receive       = null;

  function CProductOrderItem() {
    $this->CMbObject('product_order_item', 'order_item_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      'reference_id'  => 'notNull ref class|CProductReference',
      'order_id'      => 'notNull ref class|CProductOrder',
      'quantity'      => 'notNull num pos',
      'unit_price'    => 'currency',
      'date_received' => 'dateTime',
	    '_price'        => 'currency',
    );
  }

  function receive() {
    if (!$this->date_received) {
      $this->loadRefsFwd();
      $this->_ref_order->updateFormFields();
      if ($this->_ref_order->_count_received == count($this->_ref_order->_ref_order_items)) {
        $this->_ref_order->received = 1;
      }
      $this->date_received = mbDateTime();
      //$this->
      return true;
    } else {
      return false;
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
    if ($this->_receive) {
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
    if($this->order_id && $this->reference_id && !$this->_id) {
      $where['order_id']     = "= '$this->order_id'";
      $where['reference_id'] = "= '$this->reference_id'";

      $duplicateKey = new CProductOrderItem();
      $this->loadRefsFwd();
      
      if($duplicateKey->loadObject($where)) {
        $duplicateKey->loadRefsFwd();
        $this->_id = $duplicateKey->_id;
        $this->quantity = $duplicateKey->quantity+1;
        $this->unit_price = $duplicateKey->unit_price;
        $this->date_received = $duplicateKey->date_received;
      } else {
        $this->unit_price = $this->_ref_reference->price;
        $this->quantity = 1;
      }
    }
    return parent::store();
  }
}
?>