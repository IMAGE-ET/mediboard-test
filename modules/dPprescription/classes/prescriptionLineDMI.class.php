<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineDMI extends CMbObject {
  // DB Table key
  var $prescription_line_dmi_id = null;
  
  // DB Fields
  var $prescription_id         = null;
  var $praticien_id            = null;
  var $operation_id            = null;
  var $product_id              = null;  // code produit
  var $order_item_reception_id = null;  // code lot
  var $date                    = null;
  var $septic                  = null;
  var $type                    = null;
  
  var $_ref_prescription       = null;
  var $_ref_praticien          = null;
  var $_ref_operation          = null;
  var $_ref_product            = null;
  var $_ref_product_order_item_reception = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_dmi';
    $spec->key   = 'prescription_line_dmi_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["prescription_id"] = "ref notNull class|CPrescription";
    $specs["praticien_id"]    = "ref notNull class|CMediusers";
    $specs["operation_id"]    = "ref notNull class|COperation";
    $specs["product_id"]      = "ref notNull class|CProduct";
    $specs["order_item_reception_id"] = "ref notNull class|CProductOrderItemReception";
    $specs["date"]            = "dateTime notNull";
    $specs["septic"]          = "bool notNull default|0";
    $specs["type"]            = "enum notNull list|purchase|loan|deposit default|purchase"; // achat/pret/depot
    return $specs;
  }
  
  function check(){
    if (!$this->_id) {
      $where = array(
        "order_item_reception_id" => "= '$this->order_item_reception_id'"
      );
      $existing = $this->countList($where);
      
      $this->loadRefProductOrderItemReception();
      $item = $this->_ref_product_order_item_reception;
      
      $item->loadRefOrderItem();
      $item->_ref_order_item->loadRefsFwd();
      
      $ref = $item->_ref_order_item->_ref_reference;
      $ref->loadRefsFwd();
      
      $product = $ref->_ref_product;
      $product->loadRefStock();
      
      $quantity = $item->_ref_order_item->quantity * $ref->quantity * $product->quantity;
      
      if ($existing >= $quantity)
        return "Ce produit a atteint son nombre maximum de prescriptions";
    }
    return parent::check();
  }
  
  function loadRefPrescription(){
    $this->_ref_prescription = $this->loadFwdRef("prescription_id");  
  }
  
  function loadRefPraticien(){
    $this->_ref_praticien = $this->loadFwdRef("praticien_id");
		$this->_ref_praticien->loadRefFunction();
  }
  
  function loadRefProduct(){
    $this->_ref_product = $this->loadFwdRef("product_id");
  }
  
  function loadRefOperation(){
    $this->_ref_operation = $this->loadFwdRef("product_id");
  }
  
  function loadRefProductOrderItemReception(){
    $this->_ref_product_order_item_reception = $this->loadFwdRef("order_item_reception_id");
  }
  
  function loadRefsFwd(){
    $this->loadRefPrescription();
    $this->loadRefPraticien();
    $this->loadRefProduct();
    $this->loadRefOperation();
    $this->loadRefProductOrderItemReception(); 
  }
}

?>