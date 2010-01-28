<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductReception extends CMbObject {
	// DB Table key
	var $reception_id     = null;

	// DB Fields
	var $date             = null;
	var $societe_id       = null;
  var $group_id         = null;
  var $reference        = null;

	// Object References
	//    Multiple
	var $_ref_reception_items = null;
  var $_count_reception_items = null;
  var $_total = null;
	
	//    Single
	var $_ref_societe = null;
  var $_ref_group   = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_reception";
    $spec->key   = "reception_id";
    $spec->uniques["reference"] = array("reference");
    return $spec;
  }

	function getBackProps() {
		$backProps = parent::getBackProps();
		$backProps["reception_items"] = "CProductOrderItemReception reception_id";
		return $backProps;
	}

	function getProps() {
		$specs = parent::getProps();
    $specs['date']       = 'dateTime seekable';
    $specs['societe_id'] = 'ref class|CSociete';
    $specs['group_id']   = 'ref notNull class|CGroups';
	  $specs['reference']  = 'str notNull seekable';
    $specs['_total']     = 'currency';
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
  
  function findFromOrder($order_id) {
    $receptions = array();
    $order = new CProductOrder;
    $order->load($order_id);
    $order->loadBackRefs("order_items");
   
    foreach($order->_back["order_items"] as $order_item) {
      $r = $order_item->loadBackRefs("receptions");
      
      foreach($r as $_r) {
        if (!$_r->reception_id) continue;
        
        if (!isset($receptions[$_r->reception_id])) {
          $receptions[$_r->reception_id] = 0;
        }
        $receptions[$_r->reception_id]++;
      }
    }
    
    if (!count($receptions)) return;
    
    $reception_id = array_search(max($receptions), $receptions);
    if ($reception_id) {
      $this->load($reception_id);
    }
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
		$this->_ref_reception_items = $this->loadBackRefs('reception_items');
	}

  function updateTotal(){
    $this->loadRefsBack();
    $total = 0;
    foreach($this->_ref_reception_items as $_item) {
      $_item->loadRefOrderItem();
      $total += $_item->_ref_order_item->_price;
    }
    $this->_total = $total;
  }

  // @todo: supprimer ceci
  function countReceptionItems(){
    $this->_count_reception_items = $this->countBackRefs('reception_items');
  }
	
  function loadRefSociete(){
    $this->_ref_societe = $this->loadFwdRef("societe_id", true);
  }
  
	function loadRefsFwd(){
		$this->loadRefSociete();
    $this->_ref_group = $this->loadFwdRef("group_id", true);
	}

	function getPerm($permType) {
		if(!$this->_ref_reception_items) {
			$this->loadRefsBack();
		}

		foreach ($this->_ref_reception_items as $item) {
			if (!$item->getPerm($permType)) {
				return false;
			}
		}
		return true;
	}
}
