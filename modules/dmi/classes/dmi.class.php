<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass('dmi', 'produit_prescriptible');

class CDMI extends CProduitPrescriptible {
  // DB Table key
  var $dmi_id  = null;
  
  // DB Fields
  var $category_id = null;
  var $product_id  = null;
  var $code_lpp    = null;
  var $type        = null;
  
  var $_scc_code = null;
  var $_ref_product = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dmi';
    $spec->key   = 'dmi_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["category_id"] = "ref notNull class|CDMICategory autocomplete|nom";
    $specs["product_id"]  = "ref class|CProduct autocomplete|name seekable";
    $specs["code_lpp"]    = "str protected";
    $specs["code"]        = "str show|0";
    $specs["type"]        = "enum notNull list|purchase|loan|deposit default|deposit"; // achat/pret/depot
    $specs["_scc_code"]   = "str length|10";
    return $specs;
  }
       
  function loadGroupList() {
    $group = CGroups::loadCurrent();
    $dmi_category = new CDMICategory();
   	$dmi_category->group_id = $group->_id;
   	$dmi_categories_list = $dmi_category->loadMatchingList('nom');
   	$dmi_list = array();
   	foreach ($dmi_categories_list as &$cat) {
   	  $cat->loadRefsElements();
   	  $dmi_list = array_merge($dmi_list, $cat->_ref_elements);
   	}
    return $dmi_list;
  }
  
  function check(){
    if(!$this->_id){
      $group_id = CGroups::loadCurrent()->_id;
      $dmi = new CDMI();
      $ljoin["dmi_category"] = "dmi_category.category_id = dmi.category_id";
      $where["code"] = " = '$this->code'";
      $where["dmi_category.group_id"] = " = '$group_id'";
      $dmi->loadObject($where, null, null, $ljoin);
      if($dmi->_id){
        return "Un DMI possède déjà le code produit suivant: $this->code";
      }
    }
    return parent::check();
  }
  
  function loadRefsFwd(){
    $this->loadRefProduct();
  }
  
  function loadRefProduct(){
    $product = new CProduct;
    $product->load($this->product_id);
    $this->_ref_product = $product;
    $this->_produit_existant = ($this->_ref_product->_id) ? 1 : 0;
    return $this->_ref_product;
  }
  
  // FIXME: SCC pas toujours sauvegardé 
  function store() {
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->_scc_code) {
      $this->loadRefProduct();
      if ($this->_ref_product->_id) {
        $this->_ref_product->scc_code = $this->_scc_code;
        $this->_ref_product->store();
      }
    }
  }
  
  static function getFromProduct(CProduct $product){
    $dmi = new self;
    $dmi->product_id = $product->_id;
    $dmi->loadMatchingObject();
    return $dmi;
  }
}

?>