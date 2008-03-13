<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id    = mbGetValueFromGetOrSession('order_id');
$group_id    = mbGetValueFromGetOrSession('group_id');

// Loads the expected Order
$order = new CProductOrder();

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  $order->loadRefsFwd();
}

// Retrieving the Groups list
$group = new CGroups();
$list_groups = $group->loadList();
$group->load($group_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order',       $order);
$smarty->assign('group',       $group);
$smarty->assign('list_groups', $list_groups);

$smarty->display('vw_aed_order_reception.tpl');
?>
