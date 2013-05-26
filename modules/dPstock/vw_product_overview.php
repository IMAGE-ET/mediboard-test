<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkEdit();

$product_id = CValue::getOrSession('product_id');

// Loads the required Product and its References
$product = new CProduct();
if ($product->load($product_id)) {
  $product->loadRefs();
  
  foreach ($product->_ref_references as $_item) {
    $_item->loadRefs();
  }
  
  foreach ($product->_ref_stocks_group as $_item) {
    $_item->loadRefs();
  }
  
  foreach ($product->_ref_stocks_service as $_item) {
    $_item->loadRefs();
  }
}

$product->loadRefStock();

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('product', $product);
$smarty->display('vw_product_overview.tpl');
