<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$category = new CCategoryPrescription();

// Chargement de la liste des categories
$categories = $category->loadCategoriesByChap(null, "current");

// Chargement des etablissement
$group = new CGroups();
$groups = $group->loadList();

// Chargement de la category
$category_id = mbGetValueFromGetOrSession("category_id");
$category->load($category_id);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("groups"       , $groups);
$smarty->assign("categories"   , $categories);
$smarty->assign("category"     , $category);

$smarty->display("vw_edit_category.tpl");

?>