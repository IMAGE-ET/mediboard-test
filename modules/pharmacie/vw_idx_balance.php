<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_selection_id = CValue::getOrSession("product_selection_id"); //
$category_id          = CValue::getOrSession("category_id"); //
$classe_comptable     = CValue::getOrSession("classe_comptable");
$supplier_id          = CValue::getOrSession("supplier_id");
$manuf_id             = CValue::getOrSession("manuf_id");
$classe_atc           = CValue::getOrSession("_classe_atc");
$hors_t2a             = CValue::getOrSession("hors_t2a");
$include_void_service = CValue::getOrSession("include_void_service");

$_date_min            = CValue::getOrSession("_date_min");
$_date_max            = CValue::getOrSession("_date_max", mbDate());
$keywords             = CValue::getOrSession("keywords");

$product_id = CValue::getOrSession("product_id");
$year       = CValue::getOrSession("year", mbTransformTime(null, null, "%Y"));
$month      = CValue::getOrSession("month", mbTransformTime(null, null, "%m"));

$stock = new CProductStockGroup;
$stock->product_id = $product_id;
$stock->loadRefsFwd();

$product = new CProduct;
$product->classe_comptable = $classe_comptable;
$product->_classe_atc = $classe_atc;

$reference = new CProductReference;

$selection = new CProductSelection;
$list_selections = $selection->loadList(null, "name");

$category = new CProductCategory;
$list_categories = $category->loadList(null, "name");

$societe = new CSociete;
$list_societes = $societe->loadList(null, "name");

$delivery = new CProductDelivery;
$delivery->_date_min = $_date_min;
$delivery->_date_max = $_date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock',           $stock);
$smarty->assign('product',         $product);
$smarty->assign('reference',       $reference);
$smarty->assign('delivery',        $delivery);

$smarty->assign('product_selection_id', $product_selection_id);
$smarty->assign('category_id',          $category_id);
$smarty->assign('supplier_id',          $supplier_id);
$smarty->assign('manuf_id',             $manuf_id);
$smarty->assign('hors_t2a',             $hors_t2a);
$smarty->assign('include_void_service', $include_void_service);
$smarty->assign('keywords',             $keywords);

$year_now = mbTransformTime(null, null, "%Y");
$smarty->assign('years',           range($year_now, $year_now-20));
$smarty->assign('months',          range(1, 12));
$smarty->assign('year', $year);
$smarty->assign('month', $month);

$smarty->assign('list_selections', $list_selections);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);

$smarty->display('vw_idx_balance.tpl');

