<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$order_id = mbGetValueFromGet('order_id');

// Loads the expected Order
$order = new CProductOrder();
$order->load($order_id);
$order->loadRefsBack();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order', $order);
$smarty->assign('hide_products_list', true);

$smarty->display('inc_order.tpl');
?>
