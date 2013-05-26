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
 
CCanDo::checkEdit();

$reception_id = CValue::get('reception_id');
$order_id     = CValue::get('order_id');
$letter       = CValue::getOrSession('letter', "%");

$reception = new CProductReception();

if ($order_id) {
  $reception->findFromOrder($order_id);
}
else {
  $reception->load($reception_id);
}
  
$reception->loadRefsBack();
foreach ($reception->_ref_reception_items as $_reception) {
  $_reception->loadRefOrderItem()->loadReference();
}

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

$order = new CProductOrder;
$order->load($order_id);
$order->updateCounts();

foreach ($order->_ref_order_items as $_id => $_item) {
  if (!$_item->renewal) {
    unset($order->_ref_order_items[$_id]);
  }
}

foreach ($order->_ref_order_items as $_item) {
  $_item->loadRefsReceptions();
  foreach ($_item->_ref_receptions as $_reception) {
    $_reception->loadRefReception();
  }
}

if (!$reception->bill_date) {
  $reception->bill_date = CMbDT::date();
}

$order->loadView();

$smarty = new CSmartyDP();

$smarty->assign('reception', $reception);
$smarty->assign('order', $order);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('letter',          $letter);

$smarty->display('vw_edit_reception.tpl');
