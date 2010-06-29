<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$keywords    = trim(CValue::post("_view"));
$category_id = CValue::post("category_id");

$is_code128 = preg_match('/^[0-9a-z]+@[0-9a-z]+[0-9a-z\@]*$/i', $keywords);

$scc_code = null;
$scc_code_part = null;
$lot_number = null;
$lapsing_date = null;

$matches = array();
$composition = array();

/*
-- pour trouver des lots ayant le meme code : 
SELECT 
product_order_item_reception.code AS c1,  
product_order_item_reception.code AS c2,  
product_order_item_reception.`order_item_reception_id` AS i1,  
product_order_item_reception.`order_item_reception_id` AS i2  
FROM `product_order_item_reception` HAVING c1 = c2 && i1 != i2
*/

if ($is_code128) {
  $parts = explode("@", $keywords);
  
  foreach($parts as $p) {
    foreach(CDMI::$code128_prefixes as $code => $text) {
      //if (strpos($p, $code) === 0) { // strpos won't work :(
      if (substr($p, 0, strlen($code)) == $code) {
        $composition[$code] = substr($p, strlen($code), strlen($p)-strlen($code));
        break;
      }
    }
  }
}
else {
  if (preg_match('/^(?:(01)(\d{14}))?(10)([a-z0-9]{7,})(17)(\d{6})$/i', $keywords, $parts) ||
      preg_match('/^(?:(01)(\d{14}))?(17)(\d{6})(10)([a-z0-9]{7,})$/i', $keywords, $parts) ||
      preg_match('/^(01)(\d{14})$/i', $keywords, $parts)) {
    $prop = null;
    foreach($parts as $p){
      if (in_array($p, array("01", "10", "17"))) {
        $prop = $p;
      }
      else if ($prop) {
        $composition[$prop] = $p;
      }
      else $prop = null;
    }
  }
}

// EAN code (13 digits)
if (!count($composition) && preg_match('/^(\d{13})$/i', $keywords, $parts)) {
  $composition['01'] = "0".$parts[1];
}

if (count($composition)){
  $scc_code      = CValue::read($composition, "01");
  $scc_code_part = substr($scc_code, 3, 10);
  $lot_number    = CValue::read($composition, "10");
  $lapsing_date  = CValue::read($composition, "17");
}
else {
  $lot_number = $keywords;
}

// recherche par numero de lot
if ($lot_number) {
  $product_reception = new CProductOrderItemReception;
  $product_reception->code = $lot_number;
  $receptions = $product_reception->loadMatchingList();
  
  foreach($receptions as $_reception) {
    $_reception->loadRefOrderItem();
    $_reception->_ref_order_item->loadReference();
    $_reception->_ref_order_item->_ref_reference->loadRefProduct();
    $product = $_reception->_ref_order_item->_ref_reference->_ref_product;
    $matches[$product->_id] = $product;
  }
}

// recherche par code SCC
if ($scc_code_part) {
  $product = new CProduct;
  $product->scc_code = $scc_code_part;
  $matches = $product->loadMatchingList();
}

// recherche par mot clé
if (count($matches) == 0) {
  $where = array();
  if($category_id){
    $where["category_id"] = " = '$category_id'";
  }
  $lot_number = null;
  $product = new CProduct();
  
  if ($scc_code) {
    $sub_scc_code = substr($scc_code, 3, 10);
    $where["scc_code"] = "= '$sub_scc_code'";
    $matches = $product->loadList($where, "name", 30);
  }
  else {
    $matches = $product->seek($keywords, $where, 30, false, null, "name");
  }
}

// chargement des données des produits
foreach($matches as $_product) {
  $ljoin = array(
    "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
    "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
  );
  
  $where = array(
    "product_reference.product_id" => "= '$_product->_id'"
  );
  
  $reception = new CProductOrderItemReception;
  $receptions = $reception->loadList($where, null, null, null, $ljoin);
  
  $remaining = 0;
  foreach($receptions as $_reception) {
    $remaining += $_reception->getQuantity() - $_reception->countBackRefs("lines_dmi");
  }
  
  $_product->_available_quantity = $remaining;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);

$smarty->assign("scc_code_part", $scc_code_part);
$smarty->assign("lapsing_date", $lapsing_date);
$smarty->assign("lot_number", $lot_number);

$smarty->display("httpreq_do_product_autocomplete.tpl");
