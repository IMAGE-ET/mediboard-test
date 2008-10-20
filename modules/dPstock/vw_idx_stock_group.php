<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$stock_id            = mbGetValueFromGetOrSession('stock_id');
$product_id          = mbGetValueFromGetOrSession('product_id');
$category_id         = mbGetValueFromGetOrSession('category_id');

// Loads the stock in function of the stock ID or the product ID
$stock = new CProductStockGroup();

// If stock_id has been provided, we load the associated product
if ($stock_id) {
  $stock->stock_id = $stock_id;
  $stock->loadMatchingObject();
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();

// else, if a product_id has been provided, we load the associated stock
} else if($product_id) {
	$product = new CProduct();
  $product->load($product_id);
  
  $stock->product_id = $product_id;
  $stock->_ref_product = $product;
} else {
  $stock->loadRefsFwd();
}
$stock->updateFormFields();

// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock',           $stock);
$smarty->assign('category_id',     $category_id);
$smarty->assign('list_categories', $list_categories);

$smarty->display('vw_idx_stock_group.tpl');

?>