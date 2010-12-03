<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_id = CValue::get("product_id");
$year       = CValue::get("year", mbTransformTime(null, null, "%Y"));
$month      = CValue::get("month", mbTransformTime(null, null, "%m"));
$include_void_service = CValue::get("include_void_service");

CValue::setSession("product_id", $product_id);

$products = array();

$product = new CProduct;
$stock = new CProductStockGroup;

if ($product->load($product_id)) {
  $products = array($product->_id => $product);
  $stock->product_id = $product->_id;
}

$services = CProductStockGroup::getServicesList();

if ($include_void_service) {
  $services["none"] = new CService;
  $services["none"]->_view = CAppUI::tr("None");
}

list($flows, $balance) = CProduct::computeBalance($products, $services, $year, $month);
$product = reset($products);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('products', $products);
$smarty->assign('total',    count($products));
$smarty->assign('services', $services);
$smarty->assign('stock',    $stock);
$smarty->assign('flows',    $flows);
$smarty->assign('balance',  $balance);
$smarty->assign('type',     "product");

$smarty->assign('title',    $product->_view);
$smarty->assign('year',     $year);
$smarty->assign('month',    $month);

$smarty->display('inc_balance_product.tpl');

?>