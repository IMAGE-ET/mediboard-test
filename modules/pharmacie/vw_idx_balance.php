<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_id = CValue::getOrSession("product_id");
$year       = CValue::getOrSession("year", mbTransformTime(null, null, "%Y"));
$month      = CValue::getOrSession("month", mbTransformTime(null, null, "%m"));

$stock = new CProductStockGroup;
$stock->product_id = $product_id;
$stock->loadRefsFwd();

$product = new CProduct;

$selection = new CProductSelection;
$list_selections = $selection->loadList(null, "name");

$category = new CProductCategory;
$list_categories = $category->loadList(null, "name");

$stock_location = new CProductStockLocation;
$list_locations = $stock_location->loadList(null, "position, name");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock',    $stock);
$smarty->assign('product',    $product);
$smarty->assign('list_selections', $list_selections);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_locations',  $list_locations);

$smarty->display('vw_idx_balance.tpl');

