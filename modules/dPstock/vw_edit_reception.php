<?php /* $Id: vw_aed_order.php 7645 2009-12-17 16:40:57Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7645 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$order_id     = CValue::get('order_id');
$reception_id = CValue::get('reception_id');

// Loads the expected Order
$order = new CProductOrder();
$order->load($order_id);

$reception = new CProductReception();
$reception->load($reception_id);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('order', $order);
$smarty->assign('reception', $reception);
$smarty->display('vw_aed_order.tpl');
