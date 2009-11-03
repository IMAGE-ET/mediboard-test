<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$item_id = CValue::get('order_item_id');

// Loads the expected Order Item
$item = new CProductOrderItem();
if ($item->load($item_id)) {
  $item->loadRefs();
  $item->_ref_reference->loadRefsFwd();
}
$item->_quantity_received = $item->quantity_received;

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('curr_item', $item);
$smarty->assign('order', $item->_ref_order);

$smarty->display('inc_order_item.tpl');

?>