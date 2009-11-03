<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

// Recuperation de la classe de la categorie
$category_class = CValue::getOrSession("category_class");
$category_id = CValue::getOrSession("category_id");

// Chargement de la categorie selectionnee
$category = new $category_class;
$category->load($category_id);

// Chargement des elements de la categorie selectionnee
$category->loadRefsElements();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("category_class", $category_class);
$smarty->assign("category_id", $category_id);
$smarty->assign("category", $category);
$smarty->display("inc_edit_category.tpl");

?>