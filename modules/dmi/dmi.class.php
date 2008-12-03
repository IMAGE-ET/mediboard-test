<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Thomas Despoix
	*/

class CDMI extends CMbObject {
// DB Table key
  var $dmi_id  = null;

  // DB fields
  var $nom         	= null;
  var $description	= null;
  var $code	= null;
  var $dans_livret	= null;
  
  var $category_id	= null;
  
  //Object reference
  var $_ref_product = null;
  
  var $_produit_existant = null;
    
function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dmi';
    $spec->key   = 'dmi_id';
    return $spec;
  }
  
 function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["nom"]			= "notNull str";
    $specs["description"]	= "text";
    $specs["code"]		= "notNull str";
    $specs["dans_livret"]	= "bool";
    $specs["category_id"]= "notNull ref class|CDMICategory";
    $specs["_produit_existant"]= "bool";
    return $specs;
  }
  
 function getSeeks() {
    return array (
      "nom" => "like",
      "description" => "like"
    );
  }
  
	function getBackRefs() {
		$backRefs = parent::getBackRefs();
		return $backRefs;
	}

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    $this->_produit_existant = 0;
  }
  
  function loadRefProduit() {
     $this->_ref_product = new CProduct;
     $this->_ref_product->code = $this->code;
     
     if($this->_ref_product->loadMatchingObject()==null)
     {
        $this->_ref_product  = new CProduct;
        $this->_produit_existant = 0;
     }
     else
     {
     	  $this->_produit_existant = 1;
     }
    }
}

?>