<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Thomas Despoix
 */

global $can;
$can->needsRead();

// Récupération des groups
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

// Chargement de la catégorie sélectionnée
$category = new CDMICategory;
$category->load(mbGetValueFromGetOrSession("category_id"));

// Chargement de toutes les catégories
$categories = $category->loadList(null, "nom");
foreach ($categories as $_category) {
	$_category->countRefsDMI();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories", $categories);
$smarty->assign("category", $category);
$smarty->assign("groups", $groups);

$smarty->display("vw_categories.tpl");


?>