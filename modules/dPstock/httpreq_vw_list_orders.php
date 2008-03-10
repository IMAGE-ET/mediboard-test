<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$type = mbGetValueFromGetOrSession('type');

$where = array();
$order = new CProductOrder();

switch ($type) {
  case 'waiting' :// Waiting orders (not sent)
    $where['date_ordered'] = 'IS NULL';
    $where['received'] = " = '0'";
    $tpl_file = 'waiting';
    break;

  case 'pending':// Pending orders (not received yet)
    $where['date_ordered'] = 'IS NOT NULL';
    $tpl_file = 'pending';
    break;

  default:
  case 'old':// Old orders (received)
    $where['received'] = " = '1'";
    $tpl_file = 'old';
    break;
}

$orders = $order->loadList($where, 'date_ordered DESC');
foreach($orders as $ord) {
  $ord->loadRefsFwd();
  $ord->updateFormFields();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders);

$smarty->display("inc_vw_list_orders_{$tpl_file}.tpl");
?>
