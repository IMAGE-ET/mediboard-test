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

if ($type != 'waiting' && 
    $type != 'pending' && 
    $type != 'old') {
	$type = 'waiting';
}

$where['date_ordered'] = 'IS '.(($type == 'waiting')?'':'NOT ').'NULL';

$orders = $order->loadList($where, 'date_ordered DESC');
$orders_filtered = array();

foreach($orders as $ord) {
  $ord->loadRefsFwd();
  $ord->updateFormFields();
  
  if ($type == 'waiting' ||
      $type == 'pending' && !$ord->_received ||
      $type == 'old'     &&  $ord->_received)	{
  	$orders_filtered[] = $ord;
  }
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders_filtered);
$smarty->assign('type',   $type);

$smarty->display('inc_vw_list_orders.tpl');
?>
