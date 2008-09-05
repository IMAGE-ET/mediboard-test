<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien Ménager
 */

class CProductStock extends CMbObject {
  // DB Table key
  var $stock_id                 = null;

  // DB Fields
  var $product_id               = null;
  var $quantity                 = null;
  var $order_threshold_critical = null;
  var $order_threshold_min      = null;
  var $order_threshold_optimum  = null;
  var $order_threshold_max      = null;
  
  // Stock percentages 
  var $_quantity                = null;
  var $_critical                = null;
  var $_min                     = null;
  var $_optmimum                = null;
  var $_max                     = null;
  // In which part of the graph the quantity is
  var $_zone                    = null;

  // Object References
  //    Single
  var $_ref_product             = null;

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'product_id' => 'notNull ref class|CProduct',
      'quantity'   => 'num notNull',
      'order_threshold_critical' => 'num',
      'order_threshold_min'      => 'num pos notNull moreEquals|order_threshold_critical',
      'order_threshold_optimum'  => 'num pos moreEquals|order_threshold_min',
      'order_threshold_max'      => 'num pos notNull moreEquals|order_threshold_optimum',
      '_quantity'                => 'pct',
      '_critical'                => 'pct',
      '_min'                     => 'pct',
      '_optimum'                 => 'pct',
      '_max'                     => 'pct',
      '_zone'                    => 'num',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view;

    // Calculation of the levels for the bargraph
    $max = max(array($this->quantity, $this->order_threshold_max)) / 100;
    if ($max > 0) {
	    $this->_quantity = $this->quantity                 / $max;
	    $this->_critical = $this->order_threshold_critical / $max;
	    $this->_min      = $this->order_threshold_min      / $max - $this->_critical;
	    $this->_optimum  = $this->order_threshold_optimum  / $max - $this->_critical - $this->_min;
	    $this->_max      = $this->order_threshold_max      / $max - $this->_critical - $this->_min - $this->_optimum;
	      
	    if ($this->quantity <= $this->order_threshold_critical) {
	      $this->_zone = 0;
	      
	    } elseif ($this->quantity <= $this->order_threshold_min) {
	      $this->_zone = 1;
	      
	    } elseif ($this->quantity <= $this->order_threshold_optimum) {
	      $this->_zone = 2;
	      
	    } else {
	      $this->_zone = 3;
	    }
  	}
  }

  function loadRefsFwd(){
    $this->_ref_product = new CProduct;
    $this->_ref_product->load($this->product_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_product) {
      $this->loadRefsFwd();
    }
    return $this->_ref_product->getPerm($permType);
  }
}
?>