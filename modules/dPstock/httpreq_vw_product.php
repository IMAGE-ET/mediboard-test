<?php /* $Id: vw_idx_product.php 9329 2010-07-01 12:48:40Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 9329 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$product_id  = CValue::getOrSession('product_id');

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
    //"date_delivery" => "IS NULL OR date_delivery = ''",
    "stock_id" => " = '{$product->_ref_stock_group->stock_id}'",
  );
  
  $delivery = new CProductDelivery;
  $product->_ref_deliveries = $delivery->loadList($where, "date_dispensation DESC, date_delivery DESC", 50);
  
  foreach($product->_ref_deliveries as $_delivery) {
    $_delivery->loadRefsBack();
  }
  
  $product->getConsumption("-3 MONTHS");
}

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('product',         $product);
$smarty->assign('list_categories', $list_categories);
$smarty->display('inc_edit_product.tpl');

?>