<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductReceptionBill extends CMbObject {
	// DB Table key
	var $bill_id         = null;

	// DB Fields
	var $date            = null;
  var $societe_id      = null;
  var $group_id        = null;
  var $reference       = null;
  
  var $_total          = null;

	// Object References
	//    Multiple
	var $_ref_bill_items = null;
	
	//    Single
	var $_ref_reception_item = null;
  var $_ref_group   = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_bill";
    $spec->key   = "bill_id";
    $spec->uniques["reference"] = array("reference");
    return $spec;
  }

	function getBackProps() {
		$backProps = parent::getBackProps();
		$backProps["bill_items"] = "CProductReceptionBillItem bill_id";
		return $backProps;
	}

	function getProps() {
		$specs = parent::getProps();
    $specs['date']        = 'dateTime seekable';
    $specs['societe_id']  = 'ref notNull class|CSociete';
    $specs['group_id']    = 'ref notNull class|CGroups';
	  $specs['reference']   = 'str notNull seekable';
    $specs['_total']      = 'currency';
		return $specs;
	}
  
  private function getUniqueNumber() {
  	$format = CAppUI::conf('dPstock CProductOrder order_number_format');
  	
    if (strpos($format, '%id') === false) {
      $format .= '%id';
    }
    
  	$format = str_replace('%id', str_pad($this->_id?$this->_id:0, 4, '0', STR_PAD_LEFT), $format);
  	return mbTransformTime(null, null, $format);
  }

	function updateFormFields() {
		parent::updateFormFields();
    $this->loadRefSociete();
    $this->_view = $this->reference . ($this->societe_id ? " - $this->_ref_societe" : "");
	}
  
  function store () {
    if (!$this->_id && empty($this->reference)) {
      $this->reference = uniqid(rand());
      if ($msg = parent::store()) return $msg;
      $this->reference = $this->getUniqueNumber();
    }
    
    return parent::store();
  }

	function loadRefsBack(){
		$this->_ref_bill_items = $this->loadBackRefs('bill_items');
	}

  function updateTotal(){
    $this->loadRefsBack();
    $total = 0;
    foreach($this->_ref_bill_items as $_item) {
      $_item->loadRefOrderItem();
      $total += $_item->_ref_order_item->_price;
    }
    $this->_total = $total;
  }
	
  function loadRefSociete(){
    $this->_ref_societe = $this->loadFwdRef("societe_id", true);
  }
  
	function loadRefsFwd(){
		$this->loadRefSociete();
    $this->_ref_group = $this->loadFwdRef("group_id", true);
	}

	function getPerm($permType) {
		if(!$this->_ref_bill_items) {
			$this->loadRefsBack();
		}

		foreach ($this->_ref_bill_items as $item) {
			if (!$item->getPerm($permType)) {
				return false;
			}
		}
		return true;
	}
}
