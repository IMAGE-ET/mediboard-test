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

$reference_id = CValue::getOrSession('reference_id');
$societe_id   = CValue::getOrSession('societe_id');
$category_id  = CValue::getOrSession('category_id');
$product_id   = CValue::getOrSession('product_id');
$keywords     = CValue::getOrSession('keywords');

// Loads the expected Reference
$reference = new CProductReference();

// If a reference ID has been provided, 
// we load it and its associated product
if ($reference_id) {
  $reference->reference_id = $reference_id;
  $reference->loadMatchingObject();
  $reference->loadRefsFwd();
  $reference->_ref_product->loadRefsFwd();

// else, if a product_id has been provided, 
// we load it and its associated reference
} else if($product_id) {
  $reference->product_id = $product_id;
  $product = new CProduct();
  $product->load($product_id);
  $reference->_ref_product = $product;

// If a supplier ID is provided, we make a corresponding reference
} else if ($societe_id) {
  $reference->societe_id = $societe_id;
}
$reference->loadRefsFwd();

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Suppliers list
$list_societes = CSociete::getSuppliers(false);

$lists = $reference->loadRefsObjects();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('reference',       $reference);
$smarty->assign('lists_objects',   $lists);
$smarty->assign('category_id',     $category_id);
$smarty->assign('societe_id',      $societe_id);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);
$smarty->assign('keywords',        $keywords);


$smarty->display('vw_idx_reference.tpl');

?>