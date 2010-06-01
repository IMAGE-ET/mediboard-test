<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$element_prescription_id = CValue::getOrSession("element_prescription_id");
$category_prescription_id = CValue::getOrSession("category_prescription_id");
$mode_duplication = CValue::getOrSession("mode_duplication");

$element_prescription = new CElementPrescription();
$element_prescription->load($element_prescription_id);

$category_prescription = new CCategoryPrescription();
$category_prescription->load($category_prescription_id);

if($mode_duplication){
  $category = new CCategoryPrescription();
	$where["chapitre"] = $category->_spec->ds->prepareIn($category->_specs["chapitre"]->_list);
	$_categories = $category->loadList($where, "nom");
	$categories = array();
	
	// Chargement et classement par chapitre
	foreach ($_categories as $categorie) {
	  $categorie->loadRefGroup();
	  $categorie->countElementsPrescription();
	  $categories[$categorie->chapitre]["$categorie->_id"] = $categorie;
	}
	ksort($categories);	
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("category_prescription", $category_prescription);

if($mode_duplication){
	$smarty->assign("categories", $categories);
  $smarty->display("inc_form_elements_duplication.tpl");
} else {
  $smarty->display("inc_form_element.tpl");
}

?>