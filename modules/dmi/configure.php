<?php /* $Id: configure.php 4725 2008-09-01 21:00:19Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision: 4725 $
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