<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass('dmi', 'produit_prescriptible');

class CDM extends CProduitPrescriptible {
  // DB Table key
  var $dm_id  = null;
  
  // DB Fields
  var $category_dm_id = null;
  var $_ref_product = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dm';
    $spec->key   = 'dm_id';
    return $spec;
  }
  
  function getProps() {
  	$props = parent::getProps();
    $props["category_dm_id"] = "ref notNull class|CCategoryDM";
    return $props;
  }
  
  function loadRefProduct(){
    $this->loadExtProduct();
    return $this->_ref_product = $this->_ext_product;
  }
  
  function loadGroupList() {
    $group = CGroups::loadCurrent();
    $dm_category = new CCategoryDM();
   	$dm_category->group_id = $group->_id;
   	$dm_categories_list = $dm_category->loadMatchingList('nom');
   	$dm_list = array();
   	foreach ($dm_categories_list as &$cat) {
   	  $cat->loadRefsElements();
   	  $dm_list = array_merge($dm_list, $cat->_ref_elements);
   	}
    return $dm_list;
  }
  
    
  function store() {
    $this->completeField("in_livret");
    // Creation du stock si le dmi est dans le livret Therapeutique
    if (!$this->_id && CModule::getActive('dPstock') && $this->in_livret) {
      
      $product = new CProduct();
      $product->code = $this->code;
      if (!$product->loadMatchingObject()) {
        $product->name = $this->nom;
        $product->description = $this->description;
        $product->category_id = CAppUI::conf("dmi $this->_class_name product_category_id");
        $product->quantity = 1;
        if ($msg = $product->store()){
          return $msg;
        }
      }
      
      $stock = new CProductStockGroup();
      $stock->group_id = CGroups::loadCurrent()->_id;
      $stock->product_id = $product->_id;
      if (!$stock->loadMatchingObject()) {
        $stock->quantity = 1;
        $stock->order_threshold_min = 1;
        $stock->order_threshold_max = 2;
        if ($msg = $stock->store()){
          return $msg;
        }
      }
    }
    return parent::store();
  }
  
  function check(){
    if(!$this->_id){
      $group_id = CGroups::loadCurrent()->_id;
      $dm = new CDM();
      $ljoin["category_dm"] = "dm.category_dm_id = category_dm.category_dm_id";
      $where["code"] = " = '$this->code'";
      $where["category_dm.group_id"] = " = '$group_id'";
      $dm->loadObject($where, null, null, $ljoin);
      if($dm->_id){
        return "Un DM possède déjà le code produit suivant: $this->code";
      }
    }
    return parent::check();
  }
}

?>