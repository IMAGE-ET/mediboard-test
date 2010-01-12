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

// Gets objects ID from Get or Session
$product_id  = CValue::getOrSession('product_id', null);
$societe_id  = CValue::getOrSession('societe_id');
$category_id = CValue::getOrSession('category_id');

// Loads the required Product and its References
$product = new CProduct();
if ($product->load($product_id)) {
  $product->loadRefsBack();
  
  foreach($product->_ref_references as $key => $value) {
    $value->loadRefsBack();
  }
}
$product->loadRefStock();

// Loads the required Category the complete list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Loads the manufacturers list
$societe = new CSociete();
$list_societes = $societe->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('product',         $product);
$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_societes',   $list_societes);
$smarty->assign('category_id',     $category_id);
$smarty->assign('societe_id',      $societe_id);

$smarty->display('vw_idx_product.tpl');

?>