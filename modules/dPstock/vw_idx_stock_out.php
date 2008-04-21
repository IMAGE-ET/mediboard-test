<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m, $g;

$can->needsEdit();

$category_id = mbGetValueFromGetOrSession('category_id');

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

$function = new CFunctions();
$list_functions = $function->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('category_id',     $category_id);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_functions',  $list_functions);

$smarty->display('vw_idx_stock_out.tpl');

?>