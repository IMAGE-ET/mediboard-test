<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI;

$category_id = mbGetValueFromGet('category_id');

if ($search_string = @$_GET[$_GET['search_field']]) {
	$where = array();
	$where['name'] = "LIKE %$search_string%";
  $product = new CProduct();
  $list_products = $product->loadList($where, 'name');
} else {
	if ($category_id == 0) {
		$product = new CProduct();
	  $list_products = $product->loadList(null, 'name');
	} else if ($category_id == -1) {
	  $list_products = array();
	} else {
	  $category = new CProductCategory();
	  $category->load($category_id);
	  $category->loadRefsBack();
	  $list_products = $category->_ref_products;
	}
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);

$smarty->display('inc_product_selector_list_products.tpl');
?>
