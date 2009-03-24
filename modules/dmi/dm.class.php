<?php

/**
	* @package Mediboard
	* @subpackage dmi
	* @version $Revision: $
 	* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
	* @author Alexis Granger
	*/

CAppUI::requireModuleClass('DMI', 'produit_prescriptible');
class CDM extends CProduitPrescriptible {
  // DB Table key
  var $dm_id  = null;
  
  // DB Fields
  var $category_dm_id = null;
  
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