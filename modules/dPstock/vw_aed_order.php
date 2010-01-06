<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$category_id = CValue::getOrSession('category_id');
$societe_id  = CValue::getOrSession('societe_id');
$_autofill   = CValue::get('_autofill');

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Suppliers list
$societe = new CSociete();
$list_societes = $societe->loadList(null, 'name');

$order = new CProductOrder;
$list_orders = $order->search("waiting", null, 30);

foreach($list_orders as $_order) {
	$_order->countBackRefs("order_items");
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('_autofill',       $_autofill);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('category_id',     $category_id);

$smarty->assign('list_societes',   $list_societes);
$smarty->assign('societe_id',      $societe_id);

$smarty->assign('list_orders',     $list_orders);

$smarty->display('vw_aed_order.tpl');
