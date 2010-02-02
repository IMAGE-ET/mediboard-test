<?php /* $Id: vw_idx_product.php 7924 2010-01-27 14:23:35Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7924 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$product_id = CValue::getOrSession('product_id');

// Loads the required Product and its References
$product = new CProduct();
if ($product->load($product_id)) {
  $product->loadRefs();
  
  foreach($product->_ref_references as $_item) {
    $_item->loadRefs();
  }
  
  foreach($product->_ref_stocks_group as $_item) {
    $_item->loadRefs();
  }
  
  foreach($product->_ref_stocks_service as $_item) {
    $_item->loadRefs();
  }
}

$product->loadRefStock();

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('product', $product);
$smarty->display('vw_product_overview.tpl');
