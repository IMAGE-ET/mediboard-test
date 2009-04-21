<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Alexis Granger
 */

global $can, $g;
$can->needsRead();

// Recuperation de la classe de la categorie
$category_class = mbGetValueFromGetOrSession("category_class");

// Chargement de toutes les categories de l'etablissement
$category = new $category_class;
$category->group_id = CGroups::loadCurrent()->_id;
$categories = $category->loadMatchingList();

foreach($categories as $_cat){
  $_cat->countElements();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("categories", $categories);
$smarty->assign("category_class", $category_class);
$smarty->display("inc_list_categories.tpl");
?>