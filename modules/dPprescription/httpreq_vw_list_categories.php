<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$category_prescription_id = CValue::getOrSession("category_prescription_id");

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

$category_prescription = new CCategoryPrescription();
$category_prescription->load($category_prescription_id);
    
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->assign("countElements", $countElements);
$smarty->assign("category_prescription", $category_prescription);
$smarty->display("inc_list_categories.tpl");

?>