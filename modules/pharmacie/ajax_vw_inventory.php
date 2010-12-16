<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$categorization = CValue::getOrSession("categorization", "classe_comptable");

$order_item_reception = new CProductOrderItemReception;
$order_item_reception->date = CValue::get("date", mbDate());

$list_by_group = array();
$list_products = array();
$levels = array();

$product = new CProduct;

switch($categorization) {
  case "atc":
    $min_length = 1;
    $max_length = 7;
    $length_to_level = array(false, 1, false, 2, false, 3, false, 4, 5);
    
    $levels = array(
      1 => true,
      2 => false,
      3 => false,
      4 => false,
      5 => false,
    );
    
    // chargement des codes CIP
    $req = new CRequest;
    $req->addTable($product->_spec->table);
    $req->addSelect("code");
    $req->addWhere(array(
      "code IS NOT NULL",
      "LENGTH(code) = 7",
      "code REGEXP '[0-9]{7}'",
      "cancelled = '0'",
    ));
    $res = $req->getRequest();
    $list_cip = CMbArray::pluck($product->_spec->ds->loadList($res), "code");
    
    $classe_atc_obj = new CBcbClasseATC;
    foreach($list_cip as $cip) {
      $list_atc_product = $classe_atc_obj->searchATCProduit($cip);
      
      if (count($list_atc_product)) {
        $matches = $product->loadList(array("code" => "= '$cip'"));
        $_product = reset($matches); // pas loadObject qui remplace l'objet
        
        foreach($list_atc_product[0]->classes as $atc_data) {
          $code = $atc_data["code"];
          $len = strlen($code);
          
          if ($len < $min_length || $len > $max_length) continue;
          
          if (!isset($list_by_group[$code])) {
            $list_by_group[$code] = array(
              "label" => "$code - {$atc_data['libelle']}",
              "list" => array(),
              "level" => $length_to_level[$len],
            );
          }
          $list_by_group[$code]["list"][] = $_product;
        }
      }
    }
    
    ksort($list_by_group);
  break;
    
  case "classe_comptable":
    // chargement des classes comptables
    $req = new CRequest;
    $req->addTable($product->_spec->table);
    $req->addSelect("classe_comptable");
    $req->addGroup("classe_comptable");
    $req->addWhere(array(
      "classe_comptable IS NOT NULL",
      "classe_comptable != ''",
      "cancelled" => "='0'",
    ));
    $res = $req->getRequest();
    $list_classe_comptable = CMbArray::pluck($product->_spec->ds->loadList($res), "classe_comptable");
    $list_classe_comptable[] = "Sans classe comptable";
    
    foreach($list_classe_comptable as $_classe_comptable) {
      $_product = new CProduct();
      $where = array(
        "classe_comptable" => ($_classe_comptable == "Sans classe comptable") ? "IS NULL" : "='$_classe_comptable'",
        "cancelled" => "='0'",
      );
      $_list_product = $_product->loadList($where, "name");
      
      $list_by_group[$_classe_comptable] = array(
        "label" => $_classe_comptable,
        "list"  => $_list_product,
        "level" => 1,
      );
    }
    
    ksort($list_by_group);
  break;
  
  case "product_category":
    $category = new CProductCategory();
    $categories = $category->loadList(null, "name");
    
    foreach($categories as $_category) {
      $_product = new CProduct();
      $where = array(
        "category_id" => "='$_category->_id'",
        "cancelled" => "='0'",
      );
      $_list_product = $_product->loadList($where, "name");
      
      $list_by_group[$_category->_id] = array(
        "label" => $_category->_view,
        "list"  => $_list_product,
        "level" => 1,
      );
    }
  break;
  
  case "supplier":
    $supplier = new CSociete();
    $suppliers = $supplier->loadList(null, "name");
    
    foreach($suppliers as $_supplier) {
      $_product = new CProduct();
      $where = array(
        "product_reference.societe_id" => "='$_supplier->_id'",
        "product.cancelled" => "='0'",
      );
      $ljoin = array(
        "product_reference" => "product_reference.product_id = product.product_id",
      );
      $_list_product = $_product->loadList($where, "name", null, null, $ljoin);
      
      if (count($_list_product)) {
        $list_by_group[$_supplier->_id] = array(
          "label" => $_supplier->_view,
          "list"  => $_list_product,
          "level" => 1,
        );
      }
    }
  break;
  
  case "location":
    $location = new CProductStockLocation();
    $locations = $location->loadList(null, "position, name");
    $location->_view = "Sans emplacement";
    $locations[] = $location;
    
    foreach($locations as $_location) {
      $_product = new CProduct();
      $where = array(
        "product_stock_group.location_id" => ($_location->_id ? "='$_location->_id'" : "IS NULL"),
        "product.cancelled" => "='0'",
      );
      $ljoin = array(
        "product_stock_group" => "product_stock_group.product_id = product.product_id",
      );
      $_list_product = $_product->loadList($where, "name", null, null, $ljoin);
      
      if (count($_list_product)) {
        $list_by_group[$_location->_id] = array(
          "label" => $_location->_view,
          "list"  => $_list_product,
          "level" => 1,
        );
      }
    }
  break;
}

foreach($list_by_group as &$_group) {
  $_list = array_values(CMbarray::pluck($_group["list"], "_id"));
  $_list = array_map("intval", $_list);
  $_group["list_id"] = $_list;
}

//mbTrace($list_by_group);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_by_group", $list_by_group);
$smarty->assign("list_products", $list_products);
$smarty->assign("categorization", $categorization);
$smarty->assign("levels", $levels);
$smarty->assign("order_item_reception", $order_item_reception);
$smarty->display('inc_vw_inventory.tpl');
