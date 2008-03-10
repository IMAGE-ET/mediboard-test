<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id   = mbGetValueFromGetOrSession('order_id');
$societe_id = mbGetValueFromGetOrSession('societe_id');

// Loads the expected Order
$order = new CProductOrder();
if ($order_id) {
  $order->load($order_id);
  $order->loadRefsFwd();
}

// Waiting orders (not sent)
$where = array();
$where['date_ordered'] = 'IS NULL';
$where['received'] = " = '0'";
$waiting_orders = $order->loadList($where);
foreach($waiting_orders as $ord) {
  $ord->loadRefsFwd();
}

// Pending orders (not received yet)
$where = array();
$where['date_ordered'] = 'IS NOT NULL';
$pending_orders = $order->loadList($where, 'date_ordered DESC');
foreach($pending_orders as $ord) {
  $ord->loadRefsFwd();
}

// Old orders (received)
$where = array();
$where['received'] = " = '1'";
$old_orders = $order->loadList($where, 'date_ordered DESC');
foreach($old_orders as $ord) {
  $ord->loadRefsFwd();
}

// Suppliers list
$societe = new CSociete();
$list_societes = $societe->loadList();

$societe->societe_id = $societe_id;
if ($societe->loadMatchingObject() && !$order->societe_id) {
  $order->societe_id = $societe_id;
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order',          $order);
$smarty->assign('waiting_orders', $waiting_orders);
$smarty->assign('pending_orders', $pending_orders);
$smarty->assign('old_orders',     $old_orders);

$smarty->assign('list_societes',  $list_societes);

$smarty->display('vw_idx_order_manager.tpl');
?>
