<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Alexis Granger
	*/

class CProduitPrescriptible extends CMbObject {
  // DB fields
  var $nom         	= null;
  var $description	= null;
  var $code	        = null;
  var $in_livret	  = null;
  
  // Object
  var $_ext_product = null;
  
  // Form fields
  var $_produit_existant = null;
  
  function getProps() {
  	$props = parent::getProps();
    $props["nom"]	     	      	= "str notNull";
    $props["description"]	      = "text";
    $props["code"]		          = "str notNull";
    $props["in_livret"]	        = "bool";
    $props["_produit_existant"] = "bool";
    return $props;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like",
      "description" => "like"
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    $this->_produit_existant = 0;
  }
  
  function loadExtProduct() {
    $this->completeField("code");
    $this->_ext_product = new CProduct;
    if(!$this->code){
      return;
    }
    $this->_ext_product->code = $this->code;
    $this->_ext_product->loadMatchingObject();
    $this->_produit_existant = ($this->_ext_product->_id) ? 1 : 0;
  }
  
  function store() {
   	$this->completeField("in_livret");
    // Creation du stock si le dmi est dans le livret Therapeutique
   	if (!$this->_id && CModule::getActive('dPstock') && $this->in_livret) {
      $product = new CProduct();
      $product->code = $this->code;
      $product->name = $this->nom;
      $product->description = $this->description;
      $product->category_id = CAppUI::conf("dmi $this->_class_name product_category_id");
      $product->quantity = 1;
      if ($msg = $product->store()){
        return $msg;
      }
      $stock = new CProductStockGroup();
      $stock->group_id = CGroups::loadCurrent()->_id;
      $stock->product_id = $product->_id;
      $stock->quantity = 1;
      $stock->order_threshold_min = 1;
      $stock->order_threshold_max = 2;
      if ($msg = $stock->store()){
        return $msg;
      }
    }
  	return parent::store();
  }
  
  function canDeleteEx(){
    if($msg = parent::canDeleteEx()){
      return $msg;
    }
    $this->loadExtProduct();
    if($this->_ext_product->_id){
      return $this->_ext_product->canDeleteEx();  
    }
  }
}

?>