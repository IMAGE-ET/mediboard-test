<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
    $props["nom"]	     	      	= "str notNull seekable";
    $props["description"]	      = "text seekable";
    $props["code"]		          = "str notNull seekable";
    $props["in_livret"]	        = "bool default|1";
    $props["_produit_existant"] = "bool";
    return $props;
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