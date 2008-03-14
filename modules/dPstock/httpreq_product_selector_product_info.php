<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI;

$product_id = mbGetValueFromGet('product_id');

$product = new CProduct();
if ($product_id) {
  $product = new CProduct();
  $product->load($product_id);
  $product->loadRefs();
}

$colors = array('#F00', '#FC3', '#1D6', '#06F', '#000');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('colors',  $colors);
$smarty->assign('product', $product);

$smarty->display('inc_product_selector_product_info.tpl');
?>
