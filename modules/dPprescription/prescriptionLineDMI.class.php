<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescriptionLineDMI class
 */
class CPrescriptionLineDMI extends CMbObject {
  // DB Table key
  var $prescription_line_dmi_id = null;
  
  // DB Fields
  var $prescription_id = null;
  var $praticien_id    = null;
  var $product_id      = null;          // code produit
  var $order_item_reception_id = null;  // code lot

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_dmi';
    $spec->key   = 'prescription_line_dmi_id';
    return $spec;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["prescription_id"]         = "ref notNull class|CPrescription";
    $specs["praticien_id"]            = "ref notNull class|CMediusers";
    $specs["product_id"]              = "ref notNull class|CProduct";
    $specs["order_item_reception_id"] = "ref notNull class|CProductOrderItemReception";
    return $specs;
  }
  
  function loadRefPrescription(){
    $this->_ref_prescription = new CPrescription();
    $this->_ref_prescription = $this->_ref_prescription->getCached($this->prescription_id);  
  }
  
  function loadRefPraticien(){
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien = $this->_ref_praticien->getCached($this->praticien_id);
  }
  
  function loadRefProduct(){
    $this->_ref_product = new CProduct();
    $this->_ref_product = $this->_ref_product->getCached($this->product_id);
  }
  
  function loadRefProductOrderItemReception(){
    $this->_ref_product_order_item_reception = new CProductOrderItemReception();
    $this->_ref_product_order_item_reception = $this->_ref_product_order_item_reception->getCached($this->order_item_reception_id);
  }
  
  function loadRefsFwd(){
    $this->loadRefPrescription();
    $this->loadRefPraticien();
    $this->loadRefProduct();
    $this->loadRefProductOrderItemReception(); 
  }
}

?>