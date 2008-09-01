<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author Alexis Granger
 */

global $can;
$can->needsAdmin();

$category = new CProductCategory();
$categories_list = $category->loadList(null, 'name');

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('categories_list', $categories_list);
$smarty->display("configure.tpl");

?>