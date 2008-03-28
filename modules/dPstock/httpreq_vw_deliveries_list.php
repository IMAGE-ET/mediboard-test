<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$product_id   = mbGetValueFromGetOrSession('product_id');
$date_min     = mbGetValueFromGet('date_min');
$date_max     = mbGetValueFromGet('date_max');
$target_class = mbGetValueFromGet('target_class');
$target_id    = mbGetValueFromGet('target_id');
$keywords     = mbGetValueFromGet('keywords');

$where = array();

if ($product_id) {
  $where['product_id'] = " = '$product_id'";
}

if ($date_min) {
  $where['date'] = " >= '$date_min'";
}

if ($date_max) {
  $where['date'] = " <= '$date_max'";
}

if ($target_class && !$target_id) {
  $where['target_class'] = " = '$target_class'";
}

if ($target_id) {
  $where['target_id'] = " = '$target_id'";
}

if ($keywords) {
  $where['description'] = " LIKE '%$keywords%'";
}

$order_by = '`product_delivery`.`date` DESC';

$delivery = new CProductDelivery();
$deliveries_list = $delivery->loadList($where, $order_by, 20);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('deliveries_list', $deliveries_list);

$smarty->display('inc_deliveries_list.tpl');
?>
