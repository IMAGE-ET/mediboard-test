<?php /* $Id: do_order_aed.php 7346 2009-11-16 22:51:04Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $g;

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');

// New order
if (CValue::post('order_id') == 0) {
	$order = new CProductOrder();
	$order->group_id     = $g;
	$order->societe_id   = CValue::post('societe_id');
	$order->order_number = CValue::post('order_number');
	$order->locked       = 0;
	$order->cancelled    = 0;
	if ($msg = $order->store()) {
		CAppUI::setMsg($msg);
	} else {
	  if (CValue::post('_autofill') == 1) {
	    $order->autofill();
	  }
		CAppUI::setMsg($do->createMsg);
		CAppUI::redirect('m=dPstock&a=vw_aed_order&dialog=1&order_id='.$order->order_id);
	}
}

$do->doIt();

?>