<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  var $_optimum                 = null;
  var $_max                     = null;
  // In which part of the graph the quantity is
  var $_zone                    = 0;
  
  var $_package_quantity        = null; // The number of packages
  var $_package_mod             = null; // The modulus of the quantity

  // Object References
  //    Single
  /**
   * @var CProduct
   */
  var $_ref_product             = null;

  function getProps() {
    $specs = parent::getProps();
    $specs['product_id']               = 'ref notNull class|CProduct autocomplete|name show|0 dependsOn|cancelled';
    $specs['quantity']                 = 'num notNull';
    $specs['order_threshold_critical'] = 'num min|0';
    $specs['order_threshold_min']      = 'num min|0 notNull moreEquals|order_threshold_critical';
    $specs['order_threshold_optimum']  = 'num min|0 moreEquals|order_threshold_min';
    $specs['order_threshold_max']      = 'num min|0 moreEquals|order_threshold_optimum';
    $specs['_quantity']                = 'pct';
    $specs['_critical']                = 'pct';
    $specs['_min']                     = 'pct';
    $specs['_optimum']                 = 'pct';
    $specs['_max']                     = 'pct';
    $specs['_zone']                    = 'num';
    $specs['_package_quantity']        = 'str';
    $specs['_package_mod']             = 'str';
    return $specs;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["discrepancies"] = "CProductDiscrepancy object_id";
	  return $backProps;
	}
  
  function getOptimumQuantity(){
    $this->completeField(
      "order_threshold_optimum",
      "order_threshold_min",
      "order_threshold_max"
    );
    
    if ($this->order_threshold_optimum) {
      return $this->order_threshold_optimum;
    }
    else if ($this->order_threshold_max) {
      return ($this->order_threshold_min + $this->order_threshold_max) / 2;
    }
    else {
      return $this->order_threshold_min * 2;
    }
  }

	function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_product->_view;
    
    $units = $this->_ref_product->_unit_quantity ? $this->_ref_product->_unit_quantity : 1;
    $this->_package_quantity = floor($this->quantity / $units);
    $this->_package_mod      = $this->quantity % $units;

    // Calculation of the levels for the bargraph
    $max = max(
      $this->quantity, 
      $this->order_threshold_min, 
      $this->order_threshold_optimum, 
      $this->order_threshold_max
    ) / 100;
    
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
    $this->_ref_product = $this->loadFwdRef("product_id", true);
  }
  
  function getPerm($permType) {
    $this->loadRefsFwd();
    return $this->_ref_product->getPerm($permType);
  }
}
?>