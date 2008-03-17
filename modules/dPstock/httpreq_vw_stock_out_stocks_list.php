<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$category_id = mbGetValueFromGetOrSession('category_id');

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');
if ($category_id) {
  $category->category_id = $category_id;
  
  $category->loadMatchingObject();
  if ($category->_id) {
    $category->loadRefs();
    
    // Loads the products list
    foreach($category->_ref_products as $prod) {
      $prod->loadRefsBack();
    }
  }
}

$function = new CFunctions();
$list_functions = $function->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('category', $category);
$smarty->assign('list_functions',  $list_functions);

$smarty->display('inc_aed_stock_out_stocks_list.tpl');

?>