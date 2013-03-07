<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CProductStock extends CMbObject {
  public $stock_id;

  // DB Fields
  public $product_id;
  public $quantity;
  public $order_threshold_critical;
  public $order_threshold_min;
  public $order_threshold_optimum;
  public $order_threshold_max;
  public $location_id;

  // Stock percentages
  public $_quantity;
  public $_critical;
  public $_min;
  public $_optimum;
  public $_max;
  // In which part of the graph the quantity is
  public $_zone = 0;

  public $_package_quantity; // The number of packages
  public $_package_mod; // The modulus of the quantity

  /** @var CProduct */
  public $_ref_product;

  /** @var CProductStockLocation */
  public $_ref_location;

  public $_ref_related_locations;

  static $allow_quantity_fractions = false;

  function getProps() {
    $props = parent::getProps();
    $props['product_id']               = 'ref notNull class|CProduct seekable autocomplete|name show|0 dependsOn|cancelled';

    $type = (CProductStock::$allow_quantity_fractions ? "float" : "num");
    $props['quantity']                 = "$type notNull";

    $props['order_threshold_critical'] = 'num min|0';
    $props['order_threshold_min']      = 'num min|0 notNull moreEquals|order_threshold_critical';
    $props['order_threshold_optimum']  = 'num min|0 moreEquals|order_threshold_min';
    $props['order_threshold_max']      = 'num min|0 moreEquals|order_threshold_optimum';
    $props['location_id']              = 'ref notNull class|CProductStockLocation autocomplete|name|true';
    $props['_quantity']                = 'pct';
    $props['_critical']                = 'pct';
    $props['_min']                     = 'pct';
    $props['_optimum']                 = 'pct';
    $props['_max']                     = 'pct';
    $props['_zone']                    = 'num';
    $props['_package_quantity']        = 'str';
    $props['_package_mod']             = 'str';
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["discrepancies"] = "CProductDiscrepancy object_id";
    $backProps["deliveries"] = "CProductDelivery stock_id";
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

    $this->_package_mod      = $this->quantity % $units;
    $this->_package_quantity = $this->quantity / $units;

    if ($this->_package_mod || !CProductStock::$allow_quantity_fractions) {
      $this->_package_quantity = floor($this->_package_quantity);
    }

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

      }
      elseif ($this->quantity <= $this->order_threshold_min) {
        $this->_zone = 1;

      }
      elseif ($this->quantity <= $this->order_threshold_optimum) {
        $this->_zone = 2;

      }
      else {
        $this->_zone = 3;
      }
    }
  }

  function store(){
    $this->completeField("location_id");

    if (!$this->location_id) {
      $location = CProductStockLocation::getDefaultLocation($this->loadRefHost(), $this->loadRefProduct());
      $this->location_id = $location->_id;
    }

    return parent::store();
  }

  /**
   * @param boolean $cache [optional]
   *
   * @return CProductStockLocation
   */
  function loadRefLocation(){
    return $this->_ref_location = $this->loadFwdRef("location_id", true);
  }

  /**
   * @param boolean $cache [optional]
   *
   * @return CProduct
   */
  function loadRefProduct($cache = true){
    return $this->_ref_product = $this->loadFwdRef("product_id", $cache);
  }

  function loadRefsFwd(){
    $this->loadRefLocation();
    $this->loadRefProduct();
  }

  function getPerm($permType) {
    return $this->loadRefProduct()->getPerm($permType) &&
           $this->loadRefHost()->getPerm($permType);
  }

  /**
   * Returns the host object
   *
   * @return CGroups|CService|CBlocOperatoire
   */
  function loadRefHost() {
    trigger_error(__METHOD__." not implemented");
  }

  /**
   * Sets the host object
   *
   * @return void
   */
  function setHost(CMbObject $host) {
    trigger_error(__METHOD__." not implemented");
  }
}

if (CAppUI::conf("dPstock CProductStock allow_quantity_fractions")) {
  CProductStock::$allow_quantity_fractions = true;
}
