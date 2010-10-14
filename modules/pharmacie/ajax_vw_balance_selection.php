<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_seletion_id = CValue::get("product_selection_id");

$product_selection = new CProductSelection;
$product_selection->load($product_seletion_id);

$list_items = $product_selection->loadRefsItems();

$list_products = CMbArray::pluck($list_items, "_ref_product");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);

$smarty->display('inc_balance_selection.tpl');
