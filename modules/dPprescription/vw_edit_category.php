<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;
$can->needsRead();

$element_prescription_id = CValue::getOrSession("element_prescription_id");
$category_id = CValue::getOrSession("category_prescription_id");
$element_prescription_to_cdarr_id = CValue::getOrSession("element_prescription_to_cdarr_id");
$mode_duplication = CValue::get("mode_duplication");

$category = new CCategoryPrescription();
$where["chapitre"] = $category->_spec->ds->prepareIn($category->_specs["chapitre"]->_list);
$_categories = $category->loadList($where, "nom");
$categories = array();
$countElements = array();

// Chargement et classement par chapitre
foreach ($_categories as $categorie) {
  $categorie->loadRefGroup();
	$categorie->countElementsPrescription();
  $categories[$categorie->chapitre]["$categorie->_id"] = $categorie;
	if(!isset($countElements[$categorie->chapitre])){
		$countElements[$categorie->chapitre] = 0;
	}
	$countElements[$categorie->chapitre] += $categorie->_count_elements_prescription;
}
ksort($categories);
  	
// Chargement des etablissement
$group = new CGroups();
$groups = $group->loadList();

$element_prescription = new CElementPrescription();
$element_prescription->load($element_prescription_id);

$element_prescription_to_cdarr = new CElementPrescriptionToCdarr();
$element_prescription_to_cdarr->load($element_prescription_to_cdarr_id);

if(!$element_prescription_id && $element_prescription_to_cdarr_id){
	$element_prescription_to_cdarr->loadRefElementPrescription();
  $element_prescription = $element_prescription_to_cdarr->_ref_element_prescription;
	$element_prescription_id = $element_prescription->_id;
}


if(!$category->_id && $element_prescription_id){
  $element_prescription->loadRefCategory();
  $category = $element_prescription->_ref_category_prescription;
}

$element_prescription->loadBackRefs("cdarrs");
$cdarrs = array();
if($element_prescription->_back["cdarrs"]){
	foreach($element_prescription->_back["cdarrs"] as $_acte_cdarr){
	  $_acte_cdarr->loadRefActiviteCdarr();
	  $_activite_cdarr =& $_acte_cdarr->_ref_activite_cdarr;
	  $cdarrs[$_activite_cdarr->type][] = $_acte_cdarr;
	}
}
ksort($cdarrs);

// Chargement de la category
$category->load($category_id);
$category->loadElementsPrescription();
foreach ($category->_ref_elements_prescription as $_element) {
	$_element->countBackRefs("cdarrs");
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("groups"       , $groups);
$smarty->assign("categories"   , $categories);
$smarty->assign("category"     , $category);
$smarty->assign("countElements", $countElements);
$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("element_prescription_to_cdarr", $element_prescription_to_cdarr);
$smarty->assign("mode_duplication", $mode_duplication);
$smarty->assign("cdarrs", $cdarrs);
$smarty->display("vw_edit_category.tpl");

?>