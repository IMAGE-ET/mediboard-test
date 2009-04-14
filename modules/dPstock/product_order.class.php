<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductOrder extends CMbObject {
	// DB Table key
	var $order_id         = null;

	// DB Fields
	var $date_ordered     = null;
	var $societe_id       = null;
  var $group_id         = null;
	var $locked           = null;
	var $order_number     = null;
	var $cancelled        = null;
	var $deleted          = null;

	// Object References
	//    Multiple
	var $_ref_order_items = null;

	//    Single
	var $_ref_societe     = null;
	var $_ref_group       = null;

	// Form fields
	var $_total           = null;
	var $_count_received  = null;
	var $_date_received   = null;
	var $_received        = null;
  var $_partial         = null;
  
  // actions
	var $_order           = null;
	var $_receive         = null;
	var $_autofill        = null;
	var $_redo            = null;
	var $_reset           = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order';
    $spec->key   = 'order_id';
    return $spec;
  }

	function getBackProps() {
		$backProps = parent::getBackProps();
		$backProps['order_items'] = 'CProductOrderItem order_id';
		return $backProps;
	}

	function getProps() {
		$specs = parent::getProps();
    $specs['date_ordered']    = 'dateTime seekable';
    $specs['societe_id']      = 'ref notNull class|CSociete';
	  $specs['group_id']        = 'ref notNull class|CGroups';
    $specs['locked']          = 'bool';
	  $specs['cancelled']       = 'bool';
	  $specs['deleted']         = 'bool';
    $specs['order_number']    = 'str maxLength|64 seekable';
    $specs['_total']          = 'currency';
    $specs['_count_received'] = 'num pos';
	  $specs['_date_received']  = 'dateTime';
    $specs['_received']       = 'bool';
    $specs['_partial']        = 'bool';
		$specs['_order']          = 'bool';
		$specs['_receive']        = 'bool';
		$specs['_autofill']       = 'bool';
		$specs['_redo']           = 'bool';
	  $specs['_reset']          = 'bool';
		return $specs;
	}

	/** Counts this received product's items */
	function countReceivedItems() {
		$count = 0;
		$this->loadRefsBack();
		if ($this->_ref_order_items) {
			foreach ($this->_ref_order_items as $item) {
				if ($item->isReceived()) {
					$count++;
				}
			}
		}
		return $count;
	}

	/** Marks every order's items as received */
	function receive() {
		if (!$this->_ref_order_items) {
		  $this->loadRefsBack();
		}

		// we mark all the items as received
		foreach ($this->_ref_order_items as $item) {
		  if (!$item->isReceived()) {
		    $reception = new CProductOrderItemReception();
		    $reception->quantity = $item->quantity - $item->_quantity_received;
		    $reception->order_item_id = $item->_id;
		    $reception->date = mbDateTime();
		    if ($msg = $reception->store()) {
		      return $msg;
		    }
		  }
		}
	}
	
  /** Fills the order in function of the stocks and future stocks */
  function autofill() {
    $this->updateFormFields();
    
    // if the order has not been ordered yet
    // and not partially received
    // and not totally received
    // and not cancelled
    // and not deleted
    if (!$this->date_ordered && !$this->_received && !$this->cancelled && !$this->deleted) {
      
      // we empty the order
      foreach($this->_ref_order_items as $item) {
      	$item->delete();
      }
    }
  	
    // we retrieve all the stocks
  	$stock = new CProductStockGroup();
  	$list_stocks = $stock->loadList();
  	
  	// for every stock
    foreach($list_stocks as $stock) {
    	$stock->updateFormFields();
    	$stock->loadRefsFwd();
    	
    	// if the stock is in the "red" or "orange" zone
    	if ($stock->_zone_future < 2) {
    		$current_stock = $stock->quantity;
    		
    		$expected_stock = $stock->order_threshold_optimum ? 
    		  $stock->order_threshold_optimum :
    		  ($stock->order_threshold_max-$stock->order_threshold_min) / 2;
    		
    		// we get the best reference for this product
    		$where = array(
    		  'product_id' => " = '{$stock->_ref_product->_id}'",
    		  'societe_id' => " = '$this->societe_id'",
    	  );
    		$orderby = 'price / quantity ASC';
    		$best_reference = new CProductReference();
    		
    		if ($best_reference->loadObject($where, $orderby) && $best_reference->quantity > 0) {
    			$best_reference->loadRefsFwd();
    			$best_reference->_ref_product->updateFormFields();
    			
    			$qty = $best_reference->quantity * $best_reference->_ref_product->_unit_quantity;
    			
      		// and we fill the order item with the good quantity of the stock's product
      		while ($current_stock < $expected_stock) {
      		  $current_stock += $qty;
      	  }
      	  
      	  // we store the new order item in the current order
      	  $order_item = new CProductOrderItem();
          $order_item->order_id = $this->_id;
      	  $order_item->quantity = $current_stock / $qty;
      	  $order_item->reference_id = $best_reference->_id;
      	  $order_item->unit_price = $best_reference->price;
      	  $order_item->store();
      	}
    	}
    }
  }
  
  /** Fills a new order with the same articles */
  function redo() {
  	$this->load();
  	$order = new CProductOrder();
  	$order->societe_id   = $this->societe_id;
  	$order->group_id     = $this->group_id;
  	$order->locked       = 0;
  	$order->cancelled    = 0;
  	$order->store();
  	$order->order_number = $order->getUniqueNumber();
  	
  	$this->loadRefsBack();
  	foreach ($this->_ref_order_items as $item) {
  		$item->loadRefs();
  		$new_item = new CProductOrderItem();
  		$new_item->reference_id = $item->reference_id;
  		$new_item->order_id = $order->order_id;
  		$new_item->quantity = $item->quantity;
  		$new_item->unit_price = $item->_ref_reference->price;
  		$new_item->store();
  	}
  }
  
  function reset() {
    $this->load();
    $this->date_ordered = '';
    $this->locked = 0;
    $this->cancelled = 0;
    
    $this->loadRefsBack();
    foreach ($this->_ref_order_items as $item) {
      foreach($item->_ref_receptions as $reception) {
        $reception->delete();
      }
    }
  }
  
  function getUniqueNumber() {
  	global $AppUI;
  	
  	$format = $AppUI->conf('dPstock CProductOrder order_number_format');
  	
  	if (!preg_match('#\%id#', $format)) {
  		$AppUI->setMsg('format de numéro de serie incorrect');
  		return;
  	}
  	$format = str_replace('%id', str_pad($this->_id?$this->_id:0, 8, '0', STR_PAD_LEFT), $format);
  	return mbTransformTime(null, null, $format);
  }

	function updateFormFields() {
		parent::updateFormFields();
		$this->loadRefs();

		$this->_count_received = $this->countReceivedItems();

		$this->_total = 0;
		if ($this->_ref_order_items) {
			foreach ($this->_ref_order_items as $item) {
				$item->updateFormFields();
				$this->_total += $item->_price;
				$this->_date_received = isset($item->_ref_receptions[0]) ? $item->_ref_receptions[0]->date : null;
			}
		}

		$this->_received = (count($this->_ref_order_items) == $this->_count_received);
		$this->_partial = !$this->_received && ($this->_count_received > 0);

		$count = count($this->_ref_order_items);
		$this->_view  = ($this->_ref_societe)?($this->_ref_societe->_view.' - '):'';
		$this->_view .= $count.' article'.(($count>1)?'s':'').', total = '.$this->_total;
	}
	
	function updateDBFields() {
		$this->updateFormFields();
		
	  if ($this->_autofill) {
	    $this->_autofill = null;
      $this->autofill();
    }
    
    if ($this->_order && !$this->date_ordered) {
      if (count($this->_ref_order_items) != 0) {
        $this->date_ordered = mbDateTime();
      }
      $this->_order = null;
    }
    
    // If the flag _receive is true, and if not every item has been received, we mark all them as received
    if ($this->_receive && !$this->_received) {
      $this->_receive = null;
      $this->receive();
    }
    
	  if ($this->_redo) {
	    $this->_redo = null;
      $this->redo();
    }
    
	  if ($this->_reset) {
	    $this->_reset = null;
      $this->reset();
    }
	}
	
	function store () {
	  parent::store();
	  if (empty($this->order_number)) {
      $this->order_number = $this->getUniqueNumber();
    }
    parent::store();
	}

	function loadRefsBack(){
		$this->_ref_order_items = $this->loadBackRefs('order_items');
	}

	function loadRefsFwd(){
		$this->_ref_societe = new CSociete();
		$this->_ref_societe = $this->_ref_societe->getCached($this->societe_id);
		
		$this->_ref_group = new CGroups();
    $this->_ref_group = $this->_ref_group->getCached($this->group_id);
	}
	
	function delete() {
		global $AppUI;
		
		$this->updateFormFields();
		if ((count($this->_ref_order_items) == 0) || !$this->date_ordered) {
			return parent::delete();
		} else if ($this->date_ordered && !$this->_received) {
			
			// TODO: here : cancel order !!
			
			return parent::delete();
		}
		return 'This order cannot be deleted';
	}

	function getPerm($permType) {
		if(!$this->_ref_order_items) {
			$this->loadRefsFwd();
		}

		foreach ($this->_ref_order_items as $item) {
			if (!$item->getPerm($permType)) {
				return false;
			}
		}
		return true;
	}
}
?>