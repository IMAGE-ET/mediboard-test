<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI;

$product_id  = mbGetValueFromGetOrSession('product_id', null);

$product = new CProduct();
$category_id = 0;
if ($product->load($product_id)) {
  $product->loadRefsFwd();
  $category_id = $product->_ref_category->_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('product',  $product);
$smarty->assign('category_id', $category_id);

$smarty->display('product_selector.tpl');

?>