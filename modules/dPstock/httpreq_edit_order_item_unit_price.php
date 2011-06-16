<?php /* $Id: httpreq_vw_object_value.php 9329 2010-07-01 12:48:40Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 9329 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$order_item_id = CValue::get('order_item_id');

$order_item = new CProductOrderItem;
$order_item->load($order_item_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order_item', $order_item);

$smarty->display('inc_edit_order_item_unit_price.tpl');
