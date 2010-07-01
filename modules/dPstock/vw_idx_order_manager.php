<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$order_id = CValue::getOrSession('order_id');
$category_id = CValue::getOrSession('category_id');

// Loads the expected Order
$order = new CProductOrder();
if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
}

$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('order', $order);
$smarty->assign('category_id', $category_id);
$smarty->assign('list_categories', $list_categories);
$smarty->display('vw_idx_order_manager.tpl');

?>