<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Stéphanie subilia
	*/

CAppUI::requireModuleClass('dmi', 'produit_prescriptible');
class CDMI extends CProduitPrescriptible {
  // DB Table key
  var $dmi_id  = null;

  // DB Fields
  var $category_id = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dmi';
    $spec->key   = 'dmi_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["category_id"] = "ref notNull class|CDMICategory";
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
}

?>