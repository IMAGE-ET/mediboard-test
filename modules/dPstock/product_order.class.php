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
  var $comments         = null;
	var $societe_id       = null;
  var $group_id         = null;
	var $locked           = null;
	var $order_number     = null;
	var $cancelled        = null;
	var $deleted          = null;
  var $received         = null;

	// Object References
	//    Multiple
	var $_ref_order_items = null;

	//    Single
	var $_ref_societe     = null;
	var $_ref_group       = null;
  var $_ref_address     = null;

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
    $specs['comments']        = 'text';
    $specs['societe_id']      = 'ref notNull class|CSociete seekable';
	  $specs['group_id']        = 'ref notNull class|CGroups';
    $specs['locked']          = 'bool';
	  $specs['cancelled']       = 'bool';
	  $specs['deleted']         = 'bool';
    $specs['received']        = 'bool';
    $specs['order_number']    = 'str maxLength|64 seekable protected';
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
    $this->loadRefsOrderItems();
    $count = 0;
    
		foreach ($this->_ref_order_items as $item) {
			if ($item->isReceived()) {
				$count++;
			}
		}
		return $count;
	}

	/** Marks every order's items as received */
	function receive() {
		$this->loadRefsOrderItems();

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
    $this->completeField('societe_id');
    
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
    		
    		$expected_stock = $stock->getOptimumQuantity();
    		
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
  	$order->order_number = uniqid(rand());
    $order->store();
    $order->order_number = $order->getUniqueNumber();
    $order->store();
  	
  	$this->loadRefsOrderItems();
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
    
    $this->loadRefsOrderItems();
    foreach ($this->_ref_order_items as $item) {
      foreach($item->_ref_receptions as $reception) {
        $reception->delete();
      }
    }
  }
  
  /**
   * @param object $type The type of orders we are looking for [waiting|locked|pending|received|cancelled]
   * @param object $keywords [optional]
   * @param object $limit = 30 [optional]
   * @return The list of orders
   */
  function search($type, $keywords = "", $limit = 30, $where = array()) {
    global $g;
    
    $leftjoin = array();
    $leftjoin['product_order_item'] = 'product_order.order_id = product_order_item.order_id';
    
    // if keywords have been provided
    if ($keywords) {
      $societe = new CSociete();
      $where_or = array();
      
      // we seek among the societes
      foreach ($societe->getSeekables() as $field => $spec) {
        $where_societe_or[] = "societe.$field LIKE '%$keywords%'";
      }
      $where_societe[] = implode(' OR ', $where_societe_or);
      
      // we seek among the orders
      foreach($this->getSeekables() as $field => $spec) {
        $where_or[] = "product_order.$field LIKE '%$keywords%'";
      }
      $where_or[] = 'product_order.societe_id ' . CSQLDataSource::prepareIn(array_keys($societe->loadList($where_societe)));
      $where[] = implode(' OR ', $where_or);
    }
    
    $orderby = 'product_order.date_ordered DESC, product_order_item_reception.date DESC';
    $where['product_order.deleted'] = " = 0";
    $where['product_order.cancelled'] = " = 0";
    $where['product_order.locked'] = " = 0";
    $where['product_order.date_ordered'] = "IS NULL";
    
    $leftjoin['product_order_item_reception'] = 
          'product_order_item.order_item_id = product_order_item_reception.order_item_id';
    
    switch ($type) {
      case 'waiting': break;
      case 'locked':
        $where['product_order.locked'] = " = 1";
        break;
      case 'pending': // pending or received are the same here but they are sorted thanks to PHP
      case 'received':
        $where['product_order.locked'] = " = 1";
        $where['product_order.date_ordered'] = "IS NOT NULL";
        break;
      default:
      case 'cancelled':
        $where['product_order.cancelled'] = " = 1";
        unset($where['product_order.locked']);
        unset($where['product_order.date_ordered']);
        break;
    }
    
    if ($g)
      $where['product_order.group_id'] = " = $g";
      
    $orders_list = $this->loadList($where, $orderby, $limit, null, $leftjoin);
    
    if ($type === 'pending') {
      $list = array();
      foreach ($orders_list as $_order) {
        if ($_order->countReceivedItems() < $_order->countBackRefs("order_items")) {
          $list[] = $_order;
        }
      }
      $orders_list = $list;
    }
    
    else if ($type === 'received') {
      $list = array();
      foreach ($orders_list as $_order) {
        if ($_order->countReceivedItems() >= $_order->countBackRefs("order_items")) {
          $list[] = $_order;
        }
      }
      $orders_list = $list;
    }
    
    return $orders_list;
  }
  
  function getUniqueNumber() {
  	$format = CAppUI::conf('dPstock CProductOrder order_number_format');
  	
    if (strpos($format, '%id') === false) {
      $format .= '%id';
    }
    
  	$format = str_replace('%id', str_pad($this->_id ? $this->_id : 0, 6, '0', STR_PAD_LEFT), $format);
  	return mbTransformTime(null, null, $format);
  }

	function updateFormFields() {
		parent::updateFormFields();
    
    $this->completeField("received");

    if (!$this->comments) {
      $group = CGroups::loadCurrent();
      if ($group->pharmacie_id) {
        $this->comments = $group->loadRefPharmacie()->soustitre;
      }
    }
    
    $items_count = $this->countBackRefs("order_items");
    $this->loadRefsFwd();
    $this->updateTotal();
    
		$this->_view  = "$this->order_number - ";
		$this->_view .= $this->societe_id ? "$this->_ref_societe - " : "";
		$this->_view .= "$items_count article".(($items_count > 1) ? 's' : '');
    
    if ($this->_total !== null) {
      $this->_view .= ", total = $this->_total ".CAppUI::conf("currency_symbol");
    }
	}
  
  function updateTotal(){
    $this->_total = 0;
    $this->loadRefsOrderItems();
    foreach ($this->_ref_order_items as $item) {
      $item->updateFormFields();
      $this->_total += $item->_price;
    }
  }
  
  function updateCounts(){
    $this->_count_received = $this->countReceivedItems();
    
    $this->loadRefsOrderItems();
    foreach ($this->_ref_order_items as $item) {
      $item->loadRefsReceptions();
      $this->_date_received = isset($item->_ref_receptions[0]) ? $item->_ref_receptions[0]->date : null;
    }
    
    $items_count = count($this->_ref_order_items);

    $this->_received = $this->received || ($items_count >= $this->_count_received);
    $this->_partial = !$this->_received && ($this->_count_received > 0);
  }
  
  function loadRefsOrderItems($force = false) {
    if ($this->_ref_order_items && !$force) {
      return $this->_ref_order_items;
    }
      
    return $this->_ref_order_items = $this->loadBackRefs('order_items');
  }
  
  function loadRefAddress(){
    $group = CGroups::loadCurrent();
    if ($group->pharmacie_id) {
      $this->_ref_address = $group->loadRefPharmacie();
    }
    else {
      $this->_ref_address = $group;
    }
    return $this->_ref_address;
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
	  if (!$this->_id && empty($this->order_number)) {
	    $this->order_number = uniqid(rand());
      if ($msg = parent::store()) return $msg;
      $this->order_number = $this->getUniqueNumber();
	  }
    
    return parent::store();
	}

	function loadRefsBack(){
		$this->loadRefsOrderItems();
	}

	function loadRefsFwd(){
    $this->_ref_societe = $this->loadFwdRef("societe_id", true);
    $this->_ref_group = $this->loadFwdRef("group_id", true);
	}
	
	function delete() {
		$items_count = $this->countBackRefs("order_items");
    
		if (($items_count == 0) || !$this->date_ordered) {
			return parent::delete();
		} else if ($this->date_ordered && !$this->_received) {
			
			// TODO: here : cancel order !!
			
			return parent::delete();
		}
		return 'This order cannot be deleted';
	}

	function getPerm($permType) {
		$this->loadRefsOrderItems();

		foreach ($this->_ref_order_items as $item) {
			if (!$item->getPerm($permType)) {
				return false;
			}
		}
		return true;
	}
}
?>