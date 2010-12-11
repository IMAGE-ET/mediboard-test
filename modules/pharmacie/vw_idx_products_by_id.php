<?php /* $Id: vw_idx_dispensation.php 10523 2010-11-02 14:51:26Z phenxdesign $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 10523 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_ids         = CValue::post("product_ids");
$show_stock_quantity = CValue::post("show_stock_quantity");

$product_ids = explode(",", $product_ids);

$product = new CProduct();
$where = array(
  "product_id" => $product->_spec->ds->prepareIn($product_ids)
);

$list_products = $product->loadList($where, "name");

if ($show_stock_quantity) {
  foreach($list_products as $_product) {
    $_product->loadRefStock();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);
$smarty->assign('show_stock_quantity', $show_stock_quantity);

$smarty->display('vw_idx_products_by_id.tpl');
