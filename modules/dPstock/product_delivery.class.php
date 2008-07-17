<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */

class CProductDelivery extends CMbObject {
  // DB Table key
  var $delivery_id  = null;

  // DB Fields
  var $stock_id      = null;
  var $date          = null;
  var $quantity      = null;
  var $code          = null; // Lot number, lapsing date
  var $function_id   = null;

  // Object References
  //    Single
  var $_ref_stock    = null;
  var $_ref_function = null;
  
  var $_do_deliver = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'stock_id'     => 'notNull ref class|CProductStockGroup',
      'date'         => 'notNull dateTime',
      'quantity'     => 'notNull num',
      'code'         => 'str maxLength|32',
      'function_id'  => 'ref class|CFunctions',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->quantity.'x '.$this->_ref_stock->_view.($this->function_id?" pour le service '{$this->_ref_function->_view}'":'');
  }
  
  function store() {
    if ($msg = $this->check()) {
      return $msg;
    }
    if (!$this->_id && $this->_do_deliver && $this->date) {
      $this->_ref_stock = new CProductStockGroup();
      $this->_ref_stock->load($this->stock_id);
      $this->_ref_stock->quantity -= $this->quantity;
      $this->_ref_stock->store();
    }
    
    $stock_service = new CProductStockService();
    $stock_service->product_id = $this->_ref_stock->product_id;
    $stock_service->function_id = $this->function_id;
    
    if ($stock_service->loadMatchingObject()) {
      $stock_service->quantity += $this->quantity;
    } else if ($this->quantity > 0) {
      $stock_service->quantity = $this->quantity;
    }
    
    if ($msg = $stock_service->store()) {
      return $msg;
    }
    
    return parent::store();
  }
  
  function check() {
  	if ($msg = parent::check()) {
  	  return $msg;
  	}
  	if (!$this->_id && $this->_do_deliver && $this->date) {
	  	$count = $this->quantity;
	  	if (!$this->_ref_stock) {
	  		$this->loadRefsFwd();
	  	}
	    if ($this->_ref_stock->quantity < $count) {
        return 'Erreur : Impossible de d�livrer ce nombre d\'articles';
	    }
  	}
  	
  	return parent::check();
  }

  function loadRefsFwd() {
    $this->_ref_stock = new CProductStockGroup();
    $this->_ref_stock->load($this->stock_id);

    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }

  function getPerm($permType) {
    if(!$this->_ref_stock || !$this->_ref_function) {
      $this->loadRefsFwd();
    }
    if ($this->_ref_function) {
      return ($this->_ref_stock->getPerm($permType) && $this->_ref_function->getPerm($permType));
    } else {
      return ($this->_ref_stock->getPerm($permType));
    }
  }
}
?>