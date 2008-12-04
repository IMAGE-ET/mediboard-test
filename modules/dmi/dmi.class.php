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
     
     if(!$this->_ref_product->loadMatchingObject())
     {
        $this->_ref_product  = new CProduct;
        $this->_produit_existant = 0;
     }
     else
     {
     	  $this->_produit_existant = 1;
     }
   }
   
   function loadGroupList() {
   	 $group = CGroups::loadCurrent();
   	 $dmi_category = new CDMICategory();
   	 $dmi_category->group_id = $group->_id;
   	 $dmi_categories_list = $dmi_category->loadMatchingList('nom');
   	 $dmi_list = array();
   	 foreach ($dmi_categories_list as &$cat) {
   	 	 $cat->loadRefsDMI();
   	 	 $dmi_list = array_merge($dmi_list, $cat->_ref_dmis);
   	 }
   	 return $dmi_list;
   }
   
   function store() {
   	 $is_new_object = !$this->_id;
   	 
   	 parent::store();
   	 
   	 $dmi = new CDMI();
   	 $dmi->code = $this->code;
     if($dmi->countMatchingList() > 1) {
       $dmi->code = str_pad($this->_id, 10, '0', STR_PAD_LEFT);
     }
     
     
   	 /*while($dmi->countMatchingList() > 1) {
	   	 $dmi->code = $this->code;
	   	 if($dmi->loadMatchingObject()) {
	   	 	 $dmi->code = str_pad(rand(0, ), 10, '0', STR_PAD_LEFT);
	   	 }
   	 }*/
   	 $this->code = $dmi->code;
   	 
     if ($is_new_object && CModule::getActive('dPstock')) {
        $product = new CProduct();
        $product->code = $this->code;
        $product->name = $this->nom;
        $product->description = $this->description;
        $product->category_id = CAppUI::conf("dmi CDMI product_category_id");
        $product->quantity = 1;
        $product->store();
        
        $stock = new CProductStockGroup();
        $stock->group_id = CGroups::loadCurrent()->_id;
        $stock->product_id = $product->_id;
        $stock->quantity = 1;
        $stock->order_threshold_min = 1;
        $stock->order_threshold_max = 2;
        $stock->store();
     }
   	 
   	 parent::store();
   }
}

?>