<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id = mbGetValueFromGet('order_id');

// Loads the expected Order
$order = new CProductOrder();
$order->load($order_id);
$order->loadRefsBack();
foreach($order->_ref_order_items as $item) {
	$item->_quantity_received = $item->quantity_received;
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order', $order);
$smarty->assign('hide_products_list', true);

$smarty->display('inc_order.tpl');
?>
