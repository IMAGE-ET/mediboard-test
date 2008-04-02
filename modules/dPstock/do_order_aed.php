<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');
$do->createMsg = 'Commande créée';
$do->modifyMsg = 'Commande modifiée';
$do->deleteMsg = 'Commande supprimée';

// New order
if ($order_id = dPgetParam($_POST, 'order_id') == 0) {
	$order = new CProductOrder();
	$order->group_id     = dPgetParam($_POST, 'group_id', null);
	$order->societe_id   = dPgetParam($_POST, 'societe_id', null);
	$order->order_number = dPgetParam($_POST, 'order_number', null);
	if ($msg = $order->store()) {
		$AppUI->setMsg($msg);
	} else {
		$AppUI->setMsg($do->createMsg);
		$AppUI->redirect('m=dPstock&a=vw_aed_order&dialog=1&order_id='.$order->_id);
	}
}

$do->doIt();

?>