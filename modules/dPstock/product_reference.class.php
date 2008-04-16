<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

class CProductReference extends CMbObject {
  // DB Table key
  var $reference_id  = null;

  // DB Fields
  var $product_id    = null;
  var $societe_id    = null;
  var $quantity      = null;
  var $price         = null;

  // Object References
  //    Single
  var $_ref_product  = null;
  var $_ref_societe  = null;

  // Form fields
  var $_unit_price   = null;

  function CProductReference() {
    $this->CMbObject('product_reference', 'reference_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'product_id'  => 'notNull ref class|CProduct',
      'societe_id'  => 'notNull ref class|CSociete',
      'quantity'    => 'notNull num pos',
      'price'       => 'notNull currency',
      '_unit_price' => 'notNull currency',
    ));
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = "{$this->_ref_product->_view} (par {$this->quantity})";
    
    if($this->quantity != 0) {
      $this->_unit_price = ($this->price / $this->quantity);
    }
  }

  function loadRefsFwd(){
    $this->_ref_product = new CProduct();
    $this->_ref_product->load($this->product_id);
    
    $this->_ref_societe = new CSociete();
    $this->_ref_societe->load($this->societe_id);
  }
  
  function check() {
  	// checks if the product reference doesn't exist yet :
  	// no other reference can have the same product_id AND societe_id
    if($this->product_id && $this->societe_id) {
      $where['product_id'] = "= '$this->product_id'";
      $where['societe_id'] = "= '$this->societe_id'";
      $where['quantity']   = "= '$this->quantity'";
      $where['reference_id'] = "!= '$this->reference_id'";
      
      $VerifDuplicateKey = new CProductReference();
      $ListVerifDuplicateKey = $VerifDuplicateKey->loadList($where);
      
      if(count($ListVerifDuplicateKey) != 0) {
        return 'Erreur : La référence produit existe déjà';
      }
    }
    
    return parent::check();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_product) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_product->getPerm($permType));
  }
}
?>