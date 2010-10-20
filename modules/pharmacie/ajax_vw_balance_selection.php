<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_selection_id = CValue::get("product_selection_id");
$category_id          = CValue::get("category_id");
$classe_comptable     = CValue::get("classe_comptable");
$classe_atc           = CValue::get("_classe_atc");
$hors_t2a             = CValue::get("hors_t2a");

$list_products = array();

$product_selection = new CProductSelection;
if($product_selection->load($product_selection_id)) {
  $list_items = $product_selection->loadRefsItems();
  $list_products = CMbArray::pluck($list_items, "_ref_product");
}

else {
  $product = new CProduct;
  $ds = $product->_spec->ds;
  
  $where = array();
  
  if ($category_id) 
    $where["category_id"] = $ds->prepare("=%", $category_id);
    
  if ($classe_comptable) 
    $where["classe_comptable"] = $ds->prepare("=%", $classe_comptable);
    
  if (CModule::getActive("bcb")) {
    $list_cip = array();
    
    if ($classe_atc) {
      $classe_atc_obj = new CBcbClasseATC;
      $list_bcb_products = $classe_atc_obj->loadRefProduitsLivret($classe_atc);
      $list_cip = array_merge($list_cip, CMbArray::pluck($list_bcb_products, "code_cip"));
    }
    
    if ($hors_t2a) {
      $bcb_produit = new CBcbProduit;
      $list_cip = array_merge($list_cip, CMbArray::pluck($bcb_produit->getHorsT2ALivret(), "code_cip"));
    }
    
    if ($classe_atc || $hors_t2a)
      $where["code"] = $ds->prepareIn($list_cip);
  }
  
  $list_products = $product->loadList($where, "name");
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);

$smarty->display('inc_balance_selection.tpl');
