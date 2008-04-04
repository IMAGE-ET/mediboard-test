<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id    = mbGetValueFromGetOrSession('order_id');
$category_id = mbGetValueFromGetOrSession('category_id');
$societe_id  = mbGetValueFromGetOrSession('societe_id');
$_autofill   = mbGetValueFromGetOrSession('_autofill');

// Loads the expected Order
$order = new CProductOrder();

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  
  foreach ($order->_ref_order_items as $item) {
  	$item->_quantity_received = $item->quantity_received;
  }
}

// Categories list
// Loads the required Category and the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

if ($category_id) {
  $category->load($category_id);
}

if ($category) {
  $category->loadRefsBack();
  
  // Loads the products list
  foreach($category->_ref_products as $prod) {
    $prod->loadRefsBack();
  }
} else $category = new CProductCategory();


// Suppliers list
$societe = new CSociete();
$list_societes = $societe->loadList();

$societe->societe_id = $societe_id;
if ($societe->loadMatchingObject() && !$order->societe_id) {
  $order->societe_id = $societe_id;
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order',           $order);
$smarty->assign('category',        $category);
$smarty->assign('_autofill',       $_autofill);
$smarty->assign('hide_references_list', false);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);

$smarty->display('vw_aed_order.tpl');

?>
