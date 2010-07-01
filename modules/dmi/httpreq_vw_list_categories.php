<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Recuperation de la classe de la categorie
$category_class = CValue::getOrSession("category_class");

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