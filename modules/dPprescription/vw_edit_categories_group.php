<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

// Chargement de tous les groupes de categories de prescription de l'etablissement courant
$cat_group = new CPrescriptionCategoryGroup();
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["group_id"] = " = '$group_id'";
$cat_groups = $cat_group->loadList($where, "libelle");

// Chargement du groupe selectionné
$cat_group_id = CValue::getOrSession("prescription_category_group_id");
$cat_group->load($cat_group_id);

// Chargement des elements du groupe selectionné
$cat_group->loadRefsCategoryGroupItems();
$cats = array();
foreach($cat_group->_ref_category_group_items as $_group_item){
	$cat_key = $_group_item->category_prescription_id ? $_group_item->category_prescription_id : $_group_item->type_produit;
  $cats[$cat_key] = $_group_item->_id;
}

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap(null, "current");
$categories["medicaments"] = array("med", "inj", "perf");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("cat_groups"  , $cat_groups);
$smarty->assign("cat_group"   , $cat_group);
$smarty->assign("categories"  , $categories);
$smarty->assign("cats"        , $cats);
$smarty->display("vw_edit_categories_group.tpl");

?>