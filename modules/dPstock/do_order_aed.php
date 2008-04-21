<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI, $g;

$class = 'CProductOrder';

$do = new CDoObjectAddEdit($class, 'order_id');
$do->createMsg = CAppUI::tr("msg-$class-create");
$do->modifyMsg = CAppUI::tr("msg-$class-modify");
$do->deleteMsg = CAppUI::tr("msg-$class-delete");

// New order
if (mbGetValueFromPost('order_id') == 0) {
	$order = new CProductOrder();
	$order->group_id     = $g;
	$order->societe_id   = mbGetValueFromPost('societe_id');
	$order->order_number = mbGetValueFromPost('order_number');
	$order->locked       = 0;
	$order->cancelled    = 0;
	if ($msg = $order->store()) {
		$AppUI->setMsg($msg);
	} else {
		$AppUI->setMsg($do->createMsg);
		//mbTrace($order);
		$AppUI->redirect('m=dPstock&a=vw_aed_order&dialog=1&order_id='.$order->order_id);
	}
}

$do->doIt();

?>