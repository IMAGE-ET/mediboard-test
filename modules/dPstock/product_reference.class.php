<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
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
  var $code          = null;

  // Object References
  //    Single
  var $_ref_product  = null;
  var $_ref_societe  = null;

  // Form fields
  var $_unit_price   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_reference';
    $spec->key   = 'reference_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['product_id']  = 'ref notNull class|CProduct';
    $specs['societe_id']  = 'ref notNull class|CSociete';
    $specs['quantity']    = 'num notNull pos';
    $specs['price']       = 'currency notNull';
    $specs['code']        = 'str';
    $specs['_unit_price'] = 'currency notNull';
    return $specs;
  }

	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
 	  $backRefs["order_items"] = "CProductOrderItem reference_id";
	  return $backRefs;
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