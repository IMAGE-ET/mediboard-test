<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
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
  var $_optmimum                = null;
  var $_max                     = null;
  // In which part of the graph the quantity is
  var $_zone                    = null;
  
  var $_package_quantity        = null; // The number of packages
  var $_package_mod             = null; // The modulus of the quantity

  // Object References
  //    Single
  var $_ref_product             = null;

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['product_id']               = 'notNull ref class|CProduct';
    $specs['quantity']                 = 'num notNull';
    $specs['order_threshold_critical'] = 'num';
    $specs['order_threshold_min']      = 'num pos notNull moreEquals|order_threshold_critical';
    $specs['order_threshold_optimum']  = 'num pos moreEquals|order_threshold_min';
    $specs['order_threshold_max']      = 'num pos notNull moreEquals|order_threshold_optimum';
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

	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["discrepancies"] = "CProductDiscrepancy object_id";
	  return $backRefs;
	}

	function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_ref_product->updateFormFields();
    $this->_view = $this->_ref_product->_view;
    $units = $this->_ref_product->_unit_quantity ? $this->_ref_product->_unit_quantity : 1;
    $this->_package_quantity = round($this->quantity / $units);
    $this->_package_mod      = $this->quantity % $units;

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
    $this->_ref_product = $this->_ref_product->getCached($this->product_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_product) {
      $this->loadRefsFwd();
    }
    return $this->_ref_product->getPerm($permType);
  }
}
?>