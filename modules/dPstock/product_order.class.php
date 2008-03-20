<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductOrder extends CMbObject {
	// DB Table key
	var $order_id         = null;

	// DB Fields
	var $name             = null;
	var $date_ordered     = null;
	var $societe_id       = null;
  var $group_id         = null;
	var $locked           = null;
	var $order_number     = null;

	// Object References
	//    Multiple
	var $_ref_order_items = null;

	//    Single
	var $_ref_societe     = null;
	var $_ref_group       = null;

	// Form fields
	var $_total           = null;
	var $_count_received  = null;
	var $_received        = null;
  var $_partial         = null;
  
  // actions
	var $_order           = null;
	var $_receive         = null;
	var $_autofill        = null;

	function CProductOrder() {
		$this->CMbObject('product_order', 'order_id');
		$this->loadRefModule(basename(dirname(__FILE__)));
	}

	function getBackRefs() {
		$backRefs = parent::getBackRefs();
		$backRefs['order_items'] = 'CProductOrderItem order_id';
		return $backRefs;
	}

	function getSpecs() {
		return array (
      'date_ordered'    => 'dateTime',
      'societe_id'      => 'notNull ref class|CSociete',
		  'group_id'        => 'notNull ref class|CGroups',
      'locked'          => 'notNull bool',
      'order_number'    => 'notNull str maxLength|64',
      '_total'          => 'currency',
      '_count_received' => 'num pos',
      '_received'       => 'bool',
      '_partial'        => 'bool',
		);
	}

	function getSeeks() {
		return array (
      'date_ordered' => 'like',
      'order_number' => 'like',
		);
	}

	/** Counts this received product's items */
	function countReceivedItems() {
		$count = 0;
		if ($this->_ref_order_items) {
			foreach ($this->_ref_order_items as $item) {
				if ($item->date_received) {
					$count++;
				}
			}
		}
		return $count;
	}

	/** Marks every product's items as received */
	function receive() {
		$this->loadRefsBack();

		// we mark all the items as received
		foreach ($this->_ref_order_items as $item) {
		  $item->receive();
		}
	}
	
  /** Fills the order in function of the stocks and future stocks */
  function autofill() {
    // we create a new order item
    $order_item = new CProductOrderItem();
    $order_item->order_id = $this->_id;
    
    $this->updateFormFields();
    
    // if the order has not been ordered yet
    // and not partially received
    // and not totally received
    if (!$this->date_ordered && !$this->_partial && !$this->_received) {
      $items = $order_item->loadMatchingList();
      
      // we empty the order
      foreach($items as $item) {
      	$item->delete();
      }
    }
  	
  	$stock = new CProductStock();
  	$list_stocks = $stock->loadList();
  	
    foreach($list_stocks as $stock) {
    	$stock->updateFormFields();
    	
    	// if the stock is in the "red" or "orange" zone
    	if ($stock->_zone_future < 2) {

    		
    		$current_stock = $stock->quantity;
    		if ($stock->order_threshold_optimum) {
    		  $expected_stock = $stock->order_threshold_optimum;
    		} else {
    		  $expected_stock = ($stock->order_threshold_max-$stock->order_threshold_min)/2;
    		}
    		
    		// we get the best reference for this product
    		$where = array();
    		$where['product_id'] = " = '{$stock->_ref_product->_id}'";
    		$orderby = 'price / quantity DESC';
    		$best_reference = new CProductReference();
    		$best_reference->loadObject($where, $orderby);
    		
    		// and we fill the order item with the good quantity of the stock's product
    		while ($current_stock < $expected_stock) {
    		  $current_stock += $best_reference->quantity;
    	  }
    	  
    	  // we store the new order item in the current order
    	  $order_item->quantity = $current_stock;
    	  $order_item->reference_id = $best_reference->_id;
    	  $order_item->unit_price = $best_reference->price / $best_reference->quantity;
    	  $order_item->store();
    	}
    }
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
			}
		}

		$this->_received = (count($this->_ref_order_items) == $this->_count_received);
		$this->_partial = !$this->_received && ($this->_count_received > 0);

		$count = count($this->_ref_order_items);
		$this->_view = $this->_ref_societe->_view.' - '.$count.' article'.(($count>1)?'s':'').', total = '.$this->_total;
	}
	
	function updateDBFields() {
		$this->loadRefsBack();

		// If the flag _receive is true, and if not every item has been received, we mark all them as received
		if ($this->_receive && $this->countReceivedItems() != count($this->_ref_order_items)) {
			$this->receive();
		}

		if ($this->_order && !$this->date_ordered) {
			if (count($this->_ref_order_items) == 0) {
				$this->_order = null;
			} else {
				$this->date_ordered = mbDateTime();
				
				// make the real order here !!!
				
				
			}
		}
		
	  if ($this->_autofill) {
      $this->autofill();
    }
	}

	function loadRefsBack(){
		$this->_ref_order_items = $this->loadBackRefs('order_items');
	}

	function loadRefsFwd(){
		$this->_ref_societe = new CSociete();
		$this->_ref_societe->load($this->societe_id);
		
		$this->_ref_group = new CGroups();
    $this->_ref_group->load($this->group_id);
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
			if (!$perm->getPerm($permType)) {
				return false;
			}
		}
		return true;
	}
}
?>