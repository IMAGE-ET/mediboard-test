<?php /* $Id: httpreq_vw_restockages_service_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$service_id          = CValue::get('service_id');
$start               = intval(CValue::getOrSession('start', 0));
$only_service_stocks = CValue::getOrSession('only_service_stocks', 1);
$only_common         = CValue::getOrSession('only_common');
$keywords            = CValue::getOrSession('keywords');
$endowment_id        = CValue::getOrSession('endowment_id');

// Calcul de date_max et date_min
$date_min = CValue::getOrSession('_date_min');
$date_max = CValue::getOrSession('_date_max');
CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);
CValue::setSession('endowment_id', $endowment_id);

$delivrance = new CProductDelivery();
$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

$group_id = CGroups::loadCurrent()->_id;

if ($endowment_id) {
  $endowment = new CProductEndowment;
  $endowment->load($endowment_id);
  $ljoin = array(
    'product' => 'product.product_id = product_endowment_item.product_id'
  );
  $endowment_items = $endowment->loadBackRefs("endowment_items", 'product.name', "$start,20", null, $ljoin);

  $stocks = array();
  foreach($endowment_items as $_item) {
    $_item->loadRefsFwd();
    $stock_service = new CProductStockService;
    $stock_service->product_id = $_item->_ref_product->_id;
    $stock_service->service_id = $service_id;
    $stock_service->loadMatchingObject();
    
    $stock = new CProductStockGroup;
    $stock->product_id = $_item->_ref_product->_id;
    $stock->group_id = $group_id;
    $stock->loadMatchingObject();
    $stock->updateFormFields();
    
    $stock->_ref_stock_service = $stock_service;
    $stock->quantity = $_item->quantity;
    $stocks[] = $stock;
  }
  
  $count_stocks = $endowment->countBackRefs("endowment_items", null, null, null, $ljoin);
}
else if ($only_service_stocks == 1 || $only_common == 1) {
  $ljoin = array(
    'product' => 'product.product_id = product_stock_service.product_id'
  );
  $where = array(
    'product_stock_service.service_id' => "= '$service_id'"
  );
  if ($only_common) {
    $where['product_stock_service.common'] = "= '1'";
  }
  $stock = new CProductStockService;
  $stocks_service = $stock->loadList($where, 'product.name', "$start,20", null, $ljoin);
  $count_stocks   = $stock->countList($where, null, null, null, $ljoin);
  
  $stocks = array();
  if ($stocks_service) {
    foreach($stocks_service as $_id => $stock_service) {
      if ($stock_service->_ref_product->cancelled) {
        continue;
      }
      //if (count($stocks) == 20) continue;
      $stock = new CProductStockGroup;
      $stock->product_id = $stock_service->_ref_product->_id;
      $stock->group_id = $group_id;
      
      if ($stock->loadMatchingObject()) {
        $stock->updateFormFields();
        $stock->_ref_stock_service = $stock_service;
        $stock->quantity = max(0, $stock_service->getOptimumQuantity() - $stock_service->quantity);
        $stocks[$stock->_id] = $stock;
      }
    }
  }
} 
else {
  $ljoin = array(
    'product' => 'product.product_id = product_stock_group.product_id'
  );
  $group        = CGroups::loadCurrent();
  $stocks       = $group->loadBackRefs('product_stocks', 'product.name', "$start,20", null, $ljoin);
  if ($stocks) {
    foreach($stocks as $_id => $stock){
      if ($stock->_ref_product->cancelled) {
        unset($stocks[$_id]);
      }
      else {
        $stock->quantity = min($stock->quantity, $stock->getOptimumQuantity());
      }
    }
  }
  $count_stocks = $group->countBackRefs('product_stocks', null, null, null, $ljoin);
}

// Load the already ordered dispensations
foreach($stocks as &$stock) {
  $stock->loadRefsFwd();
  
  $where = array(
    'product_delivery.date_dispensation' => "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'",
    'product_delivery.stock_id' => "= '$stock->_id'",
    'product_delivery.service_id' => "= '$service_id'",
    //'product.category_id' => "= '".CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id')."'"
  );
  
  $ljoin = array(
    'product_stock_group' => 'product_delivery.stock_id = product_stock_group.stock_id',
    'product' => 'product.product_id = product_stock_group.product_id',
  );
  
  $delivery = new CProductDelivery;
  $stock->_ref_deliveries = $delivery->loadList($where, 'date_dispensation', null, null, $ljoin);
}

$service = new CService();
$service->load($service_id);
$service->loadBackRefs("endowments");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('start', $start);
$smarty->assign('stocks', $stocks);
$smarty->assign('count_stocks', $count_stocks);
$smarty->assign('delivrance', $delivrance);
$smarty->assign('keywords', $keywords);
$smarty->assign('service', $service);
$smarty->assign('only_service_stocks', $only_service_stocks);
$smarty->assign('only_common', $only_common);
$smarty->assign('endowment_id', $endowment_id);

$smarty->display('inc_stock_order.tpl');
