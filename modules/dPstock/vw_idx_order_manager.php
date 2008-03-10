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
$smarty->assign('list_societes',  $list_societes);

$smarty->display('vw_idx_order_manager.tpl');
?>
