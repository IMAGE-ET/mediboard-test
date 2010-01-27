<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductReference extends CMbObject {
  // DB Table key
  var $reference_id  = null;

  // DB Fields
  var $product_id    = null;
  var $societe_id    = null;
  var $quantity      = null;
  var $price         = null;
  var $tva           = null;
  var $code          = null;
  var $supplier_code = null;
  var $mdq           = null; // minimum delivery quantity

  // Object References
  //    Single
  var $_ref_product  = null;
  var $_ref_societe  = null;

  // Form fields
  var $_unit_price   = null;
  var $_sub_unit_price   = null;
  var $_quantity = null; // The quantity of product's sub-quantity

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_reference';
    $spec->key   = 'reference_id';
    $spec->uniques["code"] = array("code");
    $spec->uniques["quantity"] = array("quantity", "product_id", "societe_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['product_id']  = 'ref notNull class|CProduct seekable';
    $specs['societe_id']  = 'ref notNull class|CSociete autocomplete|name';
    $specs['quantity']    = 'num notNull pos';
    $specs['price']       = 'currency notNull';
    $specs['tva']         = 'pct min|0 default|0';
    $specs['code']        = 'str seekable protected';
    $specs['supplier_code'] = 'str seekable';
    $specs['mdq']         = 'num min|0';
    $specs['_unit_price'] = 'currency notNull';
    $specs['_sub_unit_price'] = 'currency';
    $specs['_quantity']   = 'num min|0';
    return $specs;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
 	  $backProps["order_items"] = "CProductOrderItem reference_id";
	  return $backProps;
	}

	function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefProduct();
    
    $this->completeField("quantity", "price");
    
    $this->_view = "$this->_ref_product (par $this->quantity)";
    
    if($this->quantity) {
      $this->_unit_price = $this->price / $this->quantity;
      $this->_sub_unit_price = $this->_unit_price / $this->_ref_product->quantity;
    }
    
    $this->_quantity = max($this->_ref_product->quantity, 1) * $this->quantity;
  }
  
  function updateDBfields(){
    parent::updateDBFields();
    
    $this->completeField("quantity");
    
    if ($this->_unit_price) {
      $this->price = $this->_unit_price * $this->quantity;
    }
  }

  function loadRefsFwd(){
    $this->loadRefProduct();
    $this->loadRefSociete();
  }
  
  function loadRefProduct(){
    return $this->_ref_product = $this->loadFwdRef("product_id", true);
  }
  
  function loadRefSociete(){
    return $this->_ref_societe = $this->loadFwdRef("societe_id", true);
  }
  
  function loadRefsObjects() {
    $items = $this->loadBackRefs("order_items");
    $lists = array(
      "orders" => array(),
      "receptions" => array(),
      "bills" => array(),
    );
    
    foreach($items as $_item) {
      if ($_item->order_id) {
        $_item->loadOrder();
        $lists["orders"][$_item->order_id] = $_item->_ref_order;
      }
      
      $_item->loadBackRefs("receptions");
      foreach($_item->_back["receptions"] as $_reception) {
        if ($_reception->reception_id) {
          $_reception->loadRefReception();
          $lists["receptions"][$_reception->reception_id] = $_reception->_ref_reception;
        }
      }
    }
    
    return $lists;
  }
  
  function getPerm($permType) {
    $this->loadRefProduct();
    return $this->_ref_product->getPerm($permType);
  }
}
?>