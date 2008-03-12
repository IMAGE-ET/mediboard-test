<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI;

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_categories', $list_categories);

$smarty->display('inc_product_selector_list_categories.tpl');

?>