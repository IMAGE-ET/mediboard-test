<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

// Gets objects ID from Get or Session
$product_id  = CValue::getOrSession('product_id', null);

$societe_id  = CValue::getOrSession('societe_id');
$category_id = CValue::getOrSession('category_id');
$keywords    = CValue::getOrSession('keywords');
$letter      = CValue::getOrSession('letter', "%");
$show_all    = CValue::getOrSession('show_all');

$filter = new CProduct;
$filter->societe_id = $societe_id;
$filter->category_id = $category_id;

// Loads the required Product and its References
$product = new CProduct();
if ($product->load($product_id)) {
  $product->loadRefsBack();
  
  $endowment_item = new CProductEndowmentItem;
  $ljoin = array(
    'product_endowment'     => "product_endowment.endowment_id = product_endowment_item.endowment_id",
  );
  foreach($product->_ref_stocks_service as $_stock) {
    $where = array(
      "product_endowment.service_id" => "= '$_stock->service_id'",
      "product_endowment_item.product_id" => "= '$product->_id'",
    );
    $_stock->_ref_endowment_items = $endowment_item->loadList($where, null, null, null, $ljoin);
  }
  
  foreach ($product->_ref_references as $_reference) {
    $_reference->loadRefProduct();
    $_reference->loadRefSociete();
  }
  
  $product->loadRefStock();
  $where = array(
    "date_delivery" => "IS NULL OR date_delivery = ''",
    "stock_id" => " = '{$product->_ref_stock_group->stock_id}'",
  );
  
  $delivery = new CProductDelivery;
  $product->_ref_deliveries = $delivery->loadList($where, "date_dispensation", 100);
}

$product->getConsommation("-3 MONTHS");

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Loads the manufacturers list
$list_societes = CSociete::getManufacturers(false);
$list_potential_manufacturers = CSociete::getManufacturers();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('product',         $product);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);
$smarty->assign('list_potential_manufacturers', $list_potential_manufacturers);

$smarty->assign('filter',          $filter);
$smarty->assign('keywords',        $keywords);
$smarty->assign('letter',          $letter);
$smarty->assign('show_all',        $show_all);

$smarty->display('vw_idx_product.tpl');

?>