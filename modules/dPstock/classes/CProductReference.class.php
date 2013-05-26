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

/**
 * Product Reference
 */
class CProductReference extends CMbObject {
  public $reference_id;

  // DB Fields
  public $product_id;
  public $societe_id;
  public $quantity;
  public $price;
  public $tva;
  public $code;
  public $supplier_code;
  public $mdq; // minimum delivery quantity
  public $cancelled;

  /** @var CProduct */
  public $_ref_product;

  /** @var CSociete */
  public $_ref_societe;
  
  static $_load_lite = false;

  // Form fields
  public $_cond_price;
  
  // #TEMP#
  public $units_fixed;
  public $orig_quantity;
  public $orig_price;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_reference';
    $spec->key   = 'reference_id';
    $spec->uniques["code"] = array("code");
    $spec->uniques["quantity"] = array("quantity", "product_id", "societe_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['product_id']  = 'ref notNull class|CProduct seekable show|0';
    $specs['societe_id']  = 'ref notNull class|CSociete autocomplete|name';
    $specs['quantity']    = 'num notNull pos';
    $specs['price']       = 'currency precise notNull';
    $specs['tva']         = 'pct min|0 default|0';
    $specs['code']        = 'str maxLength|20 seekable protected';
    $specs['supplier_code'] = 'str maxLength|40 seekable';
    $specs['mdq']         = 'num min|0';
    $specs['cancelled']   = 'bool default|0 show|0';

    $specs['_cond_price']    = 'currency precise';
    
    // #TEMP#
    $specs['units_fixed']    = 'bool show|0';
    $specs['orig_quantity']  = 'num show|0';
    $specs['orig_price']     = 'currency precise show|0';
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["order_items"] = "CProductOrderItem reference_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    if (self::$_load_lite) {
      return;
    }
    
    $this->loadRefProduct(false);
    
    $this->completeField("quantity", "price");
    
    $this->_view = "{$this->_ref_product->_view} (par $this->quantity)";
    
    if ($this->quantity) {
      $this->_cond_price = round($this->price * $this->quantity, 3);
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd($cache = true){
    $this->loadRefProduct($cache);
    $this->loadRefSociete($cache);
  }

  /**
   * Load product
   *
   * @param bool $cache Use object cache
   *
   * @return CProduct
   */
  function loadRefProduct($cache = true){
    return $this->_ref_product = $this->loadFwdRef("product_id", $cache);
  }

  /**
   * Load societe
   *
   * @param bool $cache Use object cache
   *
   * @return CSociete
   */
  function loadRefSociete($cache = true){
    return $this->_ref_societe = $this->loadFwdRef("societe_id", $cache);
  }

  /**
   * Load all references objects (products, orders, etc)
   *
   * @return array
   */
  function loadRefsObjects() {
    /** @var CProductOrderItem[] $items */
    $items = $this->loadBackRefs("order_items");
    $lists = array(
      "orders" => array(),
      "receptions" => array(),
      "bills" => array(),
    );
    
    foreach ($items as $_item) {
      if ($_item->order_id) {
        $_item->loadOrder();
        $lists["orders"][$_item->order_id] = $_item->_ref_order;
      }

      /** @var CProductOrderItemReception[] $_receptions */
      $_receptions = $_item->loadBackRefs("receptions");
      foreach ($_receptions as $_reception) {
        if ($_reception->reception_id) {
          $_reception->loadRefReception();
          $lists["receptions"][$_reception->reception_id] = $_reception->_ref_reception;
        }
      }
    }
    
    return $lists;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    return $this->loadRefProduct()->getPerm($permType);
  }
}
