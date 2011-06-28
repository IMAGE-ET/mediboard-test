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
$supplier_id          = CValue::get("supplier_id");
$manuf_id             = CValue::get("manuf_id");
$classe_atc           = CValue::get("_classe_atc");
$hors_t2a             = CValue::get("hors_t2a");
$include_void_service = CValue::get("include_void_service");

$year       = CValue::get("year", mbTransformTime(null, null, "%Y"));
$month      = CValue::get("month", mbTransformTime(null, null, "%m"));

CValue::setSession("year", $year);
CValue::setSession("month", $month);

CValue::setSession("product_selection_id", $product_selection_id);
CValue::setSession("category_id", $category_id);
CValue::setSession("classe_comptable", $classe_comptable);
CValue::setSession("supplier_id", $supplier_id);
CValue::setSession("manuf_id", $manuf_id);
CValue::setSession("_classe_atc", $classe_atc);
CValue::setSession("hors_t2a", $hors_t2a);
CValue::get("include_void_service", $include_void_service);

//CMbObject::$useObjectCache = false;
set_time_limit(300);
$limit = 2000;

CProductReference::$_load_lite = true;
CProductOrderItem::$_load_lite = true;
CProductOrderItemReception::$_load_lite = true;

$list_products = array();

$product_selection = new CProductSelection;

if($product_selection->load($product_selection_id)) {
  $list_items = $product_selection->loadRefsItems();
  $list_products = CMbArray::pluck($list_items, "_ref_product");
}

else {
  $product = new CProduct;
  $ds = $product->_spec->ds;
  
  $ljoin = array();
  $where = array();
  
  if ($category_id)
    $where["product.category_id"] = $ds->prepare("=%", $category_id);
    
  if ($manuf_id)
    $where["product.societe_id"] = $ds->prepare("=%", $manuf_id);
    
  if ($supplier_id) {
    $ljoin = array(
      "product_reference" => "product_reference.product_id = product.product_id"
    );
    $where["product_reference.societe_id"] = $ds->prepare("=%", $supplier_id);
  }
  
  if ($classe_comptable) 
    $where["product.classe_comptable"] = $ds->prepare("=%", $classe_comptable);
    
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
      $where["product.code"] = $ds->prepareIn($list_cip);
  }
  
  $list_products = $product->loadList($where, "product.name", null, null, $ljoin);
}

foreach($list_products as $_product) {
  $_product->loadRefStock(true);
}

$services = CProductStockGroup::getServicesList();

if ($include_void_service) {
  $services["none"] = new CService;
  $services["none"]->_view = CAppUI::tr("None");
}

$total = count($list_products);

if ($total > $limit)
  $list_products = array_slice($list_products, 0, $limit);

list($flows, $balance) = CProduct::computeBalance($list_products, $services, $year, $month);

// filters
$product = new CProduct;
$product->classe_comptable = $classe_comptable;
$product->_classe_atc = $classe_atc;

$selection = new CProductSelection;
$selection->load($product_selection_id);

$category = new CProductCategory;
$category->load($category_id);

$supplier = new CSociete;
$supplier->load($supplier_id);

// Title
$title = array();
if ($product_selection->_id) {
  $title[] = $product_selection->_view;
}
else {
  if ($category->_id) {
    $title[] = $category->_view;
  }
  
  if ($supplier->_id) {
    $title[] = $supplier->_view;
  }
  
  if ($product->classe_comptable) {
    $title[] = "Classe comptable $product->classe_comptable";
  }
  
  if ($product->_classe_atc) {
    $title[] = "Class ATC $product->_classe_atc";
  }
  
  if ($hors_t2a) {
    $title[] = "Hors T2A";
  }
}

$title = implode(" - ", $title);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('title',   $title);

$smarty->assign('product',   $product);
$smarty->assign('selection', $selection);
$smarty->assign('category',  $category);
$smarty->assign('supplier',  $supplier);
$smarty->assign('hors_t2a',  $hors_t2a);

$smarty->assign('services', $services);
$smarty->assign('products', $list_products);
$smarty->assign('flows',    $flows);
$smarty->assign('total',    $total);
$smarty->assign('year',     $year);
$smarty->assign('month',    $month);
$smarty->assign('display',  "price");

$smarty->assign('balance',  $balance);
$smarty->assign('type',     "selection");

$smarty->display('inc_balance_product.tpl');

/*

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);

$smarty->display('inc_balance_selection.tpl');*/
