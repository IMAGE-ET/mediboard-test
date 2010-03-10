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

$element_prescription_id = CValue::get("element_prescription_id");
$category_id = CValue::getOrSession("category_prescription_id");
$mode_duplication = CValue::get("mode_duplication");

$category = new CCategoryPrescription();
$where["chapitre"] = $category->_spec->ds->prepareIn($category->_specs["chapitre"]->_list);
$_categories = $category->loadList($where, "nom");
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

// Chargement de la category
$category->load($category_id);
$category->loadElementsPrescription();

$element_prescription = new CElementPrescription();
$element_prescription->load($element_prescription_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("groups"       , $groups);
$smarty->assign("categories"   , $categories);
$smarty->assign("category"     , $category);
$smarty->assign("countElements", $countElements);
$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("mode_duplication", $mode_duplication);
$smarty->display("vw_edit_category.tpl");

?>