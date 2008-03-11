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
	// Waiting orders (not sent)
  case 'waiting' :
    $where['date_ordered'] = 'IS NULL';
    $where['received'] = " = '0'";
    $tpl_file = 'waiting';
    break;

  // Pending orders (not received yet)
  case 'pending':
    $where['date_ordered'] = 'IS NOT NULL';
    $where['received'] = " = '0'";
    $tpl_file = 'pending';
    break;

  default:
  // Old orders (received)
  case 'old':
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
