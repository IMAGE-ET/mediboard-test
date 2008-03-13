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
	var $locked           = null;
	var $order_number     = null;

	// Object References
	//    Multiple
	var $_ref_order_items = null;

	//    Single
	var $_ref_societe     = null;

	// Form fields
	var $_total           = null;
	var $_count_received  = null;
	var $_received        = null;
  var $_partial         = null;
  
  // actions
	var $_order           = null;
	var $_receive         = null;


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
      'name'            => 'str maxLength|64',
      'date_ordered'    => 'dateTime',
      'societe_id'      => 'notNull ref class|CSociete',
      'locked'          => 'notNull bool',
      'order_number'    => 'str',
      '_total'          => 'currency',
      '_count_received' => 'num pos',
      '_received'       => 'bool',
      '_partial'        => 'bool',
		);
	}

	function getSeeks() {
		return array (
      'name'         => 'like',
      'date_ordered' => 'like',
      'societe_id'   => 'like',
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
	function receiveAllItems() {
		$this->loadRefsBack();

		// we mark all the items as received
		foreach ($this->_ref_order_items as $item) {
			if ($item->date_received == null) {
				$item->_receive = true;
			}
			$item->store();
		}
	}

	function updateFormFields() {
		parent::updateFormFields();
		$this->loadRefsBack();

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
		$this->_view = $count.' article'.(($count>1)?'s':'').', total = '.$this->_total;
	}
	
	function updateDBFields() {
		$this->loadRefsBack();

		// If the flag _receive is true, and if not every item has been received, we mark all them as received
		if ($this->_receive && $this->countReceivedItems() != count($this->_ref_order_items)) {
			$this->receiveAllItems();
		}

		if ($this->_order && !$this->date_ordered) {
			if (count($this->_ref_order_items) == 0) {
				$this->_order = null;
			} else {
				$this->date_ordered = mbDateTime();
				
				// make the real order here !!!
				
				
			}
		}
	}

	function loadRefsBack(){
		$this->_ref_order_items = $this->loadBackRefs('order_items');
	}

	function loadRefsFwd(){
		$this->_ref_societe = new CSociete;
		$this->_ref_societe->load($this->societe_id);
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