<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$order_id    = mbGetValueFromGetOrSession('order_id');
$category_id = mbGetValueFromGetOrSession('category_id');
$societe_id  = mbGetValueFromGetOrSession('societe_id');
$_autofill   = mbGetValueFromGet('_autofill');

// Loads the expected Order
$order = new CProductOrder();

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  if ($_autofill) {
    $order->autofill();
  }
}

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Suppliers list
$societe = new CSociete();
$list_societes = $societe->loadList(null, 'name');


// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order',           $order);
$smarty->assign('_autofill',       $_autofill);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('category_id',     $category_id);

$smarty->assign('list_societes',   $list_societes);
$smarty->assign('category_id',     $category_id);

$smarty->display('vw_aed_order.tpl');

?>