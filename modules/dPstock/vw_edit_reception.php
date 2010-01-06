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

$reception_id = CValue::get('reception_id');
$order_id = CValue::get('order_id');

$reception = new CProductReception();

if ($order_id)
  $reception->findFromOrder($order_id);
else
  $reception->load($reception_id);
  
$reception->loadBackRefs("reception_items");

$order = new CProductOrder;
$order->load($order_id);

$smarty = new CSmartyDP();
$smarty->assign('reception', $reception);
$smarty->assign('order', $order);
$smarty->display('vw_edit_reception.tpl');
