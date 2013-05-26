<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkRead();

$order_item_id = CValue::get('order_item_id');

$order_item = new CProductOrderItem;
$order_item->load($order_item_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order_item', $order_item);

$smarty->display('inc_edit_order_item_unit_price.tpl');
