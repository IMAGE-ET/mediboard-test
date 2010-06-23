<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$product_id = CValue::get('product_id');
$keywords   = CValue::get('_view');
$is_code128 = CValue::get('_is_code128');
$lot_number = CValue::get('_lot_number');

$product = new CProduct();
$product->load($product_id);

$dmi = new CDMI;

$list_societes = array();
$list_references = array();
$list_receptions = array();

if($product->_id) {
	$where = array(
    "product.product_id" => "= $product_id"
  );
  $leftjoin = array(
    "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
    "product_reference" => "product_order_item.reference_id = product_reference.reference_id",
    "product" => "product_reference.product_id=product.product_id"
  );
	
  //chargement de la reception
  $product_order_item_reception = new CProductOrderItemReception();
  $list_receptions = $product_order_item_reception->loadList($where,null,null,null,$leftjoin);
  foreach ($list_receptions as $_poir) {
  	$_poir->loadRefsFwd();
  }
}
else {
  //$dmi->code = ($is_code128 ? "" : $keywords);
  $dmi->_lot_number = $lot_number;
  $dmi->in_livret = 1;
  /*
  $dmi_category = new CDMICategory;
  $dmi_category->loadObject(); // FIXME: devrait etre en config
  $dmi->category_id = $dmi_category->_id;*/
}

if(count($list_receptions) > 0) {
  foreach ($list_receptions as $_id => $_reception) {
    $remaining = $_reception->getQuantity() - $_reception->countBackRefs("lines_dmi");
    if ($remaining < 1) {
      unset($list_receptions[$_id]);
    }
  }
  
  if(count($list_receptions) == 0) {
    CAppUI::stepAjax("Tous les articles <strong>$product->_view</strong> sont déjà consommés", UI_MSG_WARNING);
  }
}

if ($product->_id) {
  $reference = new CProductReference;
  $reference->product_id = $product->_id;
  $list_references = $reference->loadMatchingList();
}

if (count($list_references)) {
  foreach($list_references as $_reference) {
    $_reference->loadRefSociete();
  }
}
else {
  $societe = new CSociete;
  $list_societes = $societe->loadList(null, "name");
}

$smarty = new CSmartyDP();
$smarty->assign("list", $list_receptions);
$smarty->assign("dmi", $dmi);
$smarty->assign("list_references", $list_references);
$smarty->assign("list_societes", $list_societes);
$smarty->assign("product", $product);
$smarty->assign("keywords", $keywords);
$smarty->assign("is_code128", $is_code128);
$smarty->display('inc_search_product_order_item_reception.tpl');
