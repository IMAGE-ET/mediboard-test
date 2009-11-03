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

$category = new CCategoryPrescription();
$_categories = $category->loadList(null, "nom");

// Chargement et classement par chapitre
foreach ($_categories as $categorie) {
  $categorie->loadRefGroup();
  $categories[$categorie->chapitre]["$categorie->_id"] = $categorie;
}
ksort($categories);
  	
// Chargement des etablissement
$group = new CGroups();
$groups = $group->loadList();

// Chargement de la category
$category_id = CValue::getOrSession("category_id");
$category->load($category_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("groups"       , $groups);
$smarty->assign("categories"   , $categories);
$smarty->assign("category"     , $category);

$smarty->display("vw_edit_category.tpl");

?>