<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$product_id  = mbGetValueFromGetOrSession('product_id', null);

$product = new CProduct();
$category_id = 0;
if ($product->load($product_id)) {
  $product->loadRefsFwd();
  $category_id = $product->_ref_category->_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('selected_product',  $product->_id);
$smarty->assign('selected_category', $category_id);

$smarty->display('product_selector.tpl');

?>