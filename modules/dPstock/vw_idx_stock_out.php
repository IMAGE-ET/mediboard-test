<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$category_id = mbGetValueFromGetOrSession('category_id');

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');
$category->category_id = $category_id;
$category->loadMatchingObject();

if ($category) {
  $category->loadRefsBack();
  
  // Loads the products list
  foreach($category->_ref_products as $prod) {
    $prod->loadRefsBack();
  }
} else $category = new CProductCategory();

$function = new CFunctions();
$list_functions = $function->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('category',        $category);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_functions',  $list_functions);

$smarty->display('vw_idx_stock_out.tpl');

?>