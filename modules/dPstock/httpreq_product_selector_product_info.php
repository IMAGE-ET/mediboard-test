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

CCanDo::checkRead();

$product_id = CValue::get('product_id');

$product = new CProduct();
if ($product_id) {
  $product->load($product_id);
  $product->loadRefs();
  $product->loadRefStock();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('product', $product);
$smarty->display('inc_product_selector_product_info.tpl');
