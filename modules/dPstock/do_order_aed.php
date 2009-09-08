<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $g;

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');

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
	  if (mbGetValueFromPost('_autofill') == 1) {
	    $order->autofill();
	  }
		$AppUI->setMsg($do->createMsg);
		$AppUI->redirect('m=dPstock&a=vw_aed_order&dialog=1&order_id='.$order->order_id);
	}
}

$do->doIt();

?>