<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProduct extends CMbObject {
  // DB Table key
  var $product_id        = null;

  // DB Fields
  var $name              = null;
  var $description       = null;
  var $code              = null;
  var $category_id       = null;
  var $societe_id        = null;
  var $quantity          = null;
  var $item_title        = null;
  var $unit_quantity     = null;
  var $unit_title        = null;
  var $packaging         = null;
  var $renewable         = null;
  var $cancelled         = null;
  var $classe_comptable  = null;

  // Object References
  //    Single
  var $_ref_category     = null;
  var $_ref_societe      = null;

  //    Multiple
  var $_ref_stocks_group   = null;
  var $_ref_stocks_service = null;
  var $_ref_references     = null;
  
  // Undividable quantity
  var $_unit_quantity      = null;
  var $_unit_title         = null;
  var $_quantity           = null; // The quantity view
  
  var $_unique_usage       = null;
  
  // This group's stock id
  var $_ref_stock_group    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product';
    $spec->key   = 'product_id';
    $spec->uniques["code"] = array("code");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['references']     = 'CProductReference product_id';
    $backProps['stocks_group']   = 'CProductStockGroup product_id';
    $backProps['stocks_service'] = 'CProductStockService product_id';
    $backProps['lines_dmi']      = 'CPrescriptionLineDMI product_id';
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['name']          = 'str notNull seekable';
    $specs['description']   = 'text seekable';
    $specs['code']          = 'str maxLength|32 seekable protected';
    $specs['category_id']   = 'ref notNull class|CProductCategory';
    $specs['societe_id']    = 'ref class|CSociete seekable autocomplete|name';
    $specs['quantity']      = 'num notNull min|0';
    $specs['item_title']    = 'str autocomplete';
    $specs['unit_quantity'] = 'float min|0';
    $specs['unit_title']    = 'str autocomplete';
    $specs['packaging']     = 'str autocomplete';
    $specs['renewable']     = 'enum list|0|1|2';
    $specs['cancelled']     = 'bool default|0 show|0';
    $specs['classe_comptable'] = 'str maxLength|7 autocomplete';
    
    $specs['_unit_title']   = 'str';
    $specs['_unique_usage'] = 'bool';
    $specs['_unit_quantity']= 'float min|0';
    $specs['_quantity']     = 'str';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
    
    if ($this->unit_quantity !== null && $this->unit_quantity == round($this->unit_quantity)) { // float to int (the comma is deleted)
	    $this->unit_quantity = round($this->unit_quantity);
	  }
	  if ($this->unit_quantity === 0) $this->unit_quantity = '';
    
	  $this->_quantity = '';
    if ($this->item_title && $this->quantity) {
    	$this->_quantity .= "$this->quantity $this->item_title";
    }
    
//  Unnecessary. Waiting a few days before total removal
//    if ($this->unit_quantity && $this->unit_title) {
//      $this->_quantity .= ($this->_quantity ? " x " : "") . "$this->unit_quantity $this->unit_title";
//    }
    
    if ($this->item_title && $this->quantity) {
	    $this->_unit_quantity = ($this->quantity ? $this->quantity : 1);
	    $this->_unit_title = $this->item_title;
    } else {
    	$this->_unit_quantity = ($this->unit_quantity ? $this->unit_quantity : 1);
      $this->_unit_title = $this->unit_title;
    }
    
    $this->_unique_usage = ($this->unit_quantity < 2 && !$this->renewable);
  }

  function loadRefsBack() {
  	$this->_ref_references     = $this->loadBackRefs('references');
    $this->_ref_stocks_group   = $this->loadBackRefs('stocks_group');
    $this->_ref_stocks_service = $this->loadBackRefs('stocks_service');
  }

  function loadRefsFwd() {
    $this->_ref_category = $this->loadFwdRef("category_id", true);
    $this->_ref_societe  = $this->loadFwdRef("societe_id" , true);
  }
  
  // Loads the stock associated to the current group
  function loadRefStock() {
  	global $g;
  	
    $this->_ref_stock_group = new CProductStockGroup();
    $this->_ref_stock_group->group_id = $g;
    $this->_ref_stock_group->product_id = $this->product_id;
    return $this->_ref_stock_group->loadMatchingObject();
  }

  function getPerm($permType) {
    if(!$this->_ref_category) {
      $this->loadRefsFwd();
    }
    return $this->_ref_category->getPerm($permType);
  }
  
  function updateDBFields(){
    $this->completeField('quantity', 'unit_quantity');
    
    if(!$this->quantity)          $this->quantity = 1;
    if($this->unit_quantity == 0) $this->unit_quantity = '';
    
    return parent::updateDBFields();
  }
  
  function getConsommation($since = "-1 MONTH"){
    $this->loadRefStock();
    $where = array(
      "stock_id" => "= '{$this->_ref_stock_group->_id}'",
      "product_delivery_trace.date_delivery" => " > '".mbDate($since)."'",
    );
    $ljoin = array(
      "product_delivery" => "product_delivery.delivery_id = product_delivery_trace.delivery_trace_id"
    );
    
    $trace = new CProductDeliveryTrace;
    $traces = $trace->loadList($where, null, null, null, $ljoin);

    $total = 0;
    foreach($traces as $_trace) {
      $total += $_trace->quantity;
    }
    return $total;
  }
  
  function store() {
    if ($this->fieldModified("cancelled", 1)) {
      $references = $this->loadBackRefs("references");
      foreach($references as $_ref) {
        $_ref->cancelled = 1;
        $_ref->store();
      }
    }
    
    return parent::store();
  }
}
?>