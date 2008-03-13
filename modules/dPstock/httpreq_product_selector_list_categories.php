<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien M�nager
 */

global $AppUI;

$search_string = mbGetValueFromGet('search_string');
$category_id = mbGetValueFromGet('category_id');

// Loads the required Category and the complete list
$category = new CProductCategory();
$total = null;
$count = null;

if ($search_string) {
  $where = array();
  $where['name'] = "LIKE '%$search_string%'";
  $list_categories = $category->loadList($where, 'name', 20);
  $total = $category->countList($where);
} else {
  $list_categories = $category->loadList(null, 'name');
  $total = count($list_categories);
}
$count = count($list_categories);
if ($total == $count) $total = null;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('list_categories', $list_categories);
$smarty->assign('category_id', $category_id);
$smarty->assign('count', $count);
$smarty->assign('total', $total);

$smarty->display('inc_product_selector_list_categories.tpl');

?>
