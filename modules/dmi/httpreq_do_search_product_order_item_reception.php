<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_id    = CValue::get('product_id');
$keywords      = CValue::get('_view');

$manufacturer_code = CValue::get('_manufacturer_code');
$scc_code_part = CValue::get('_scc_code_part');
$lot_number    = CValue::get('_lot_number');
$lapsing_date  = CValue::get('_lapsing_date');

if ($lapsing_date) {
  // 2016-08
  if (preg_match("/(\d{4})-(\d{2})/", $lapsing_date, $match)) {
    $lapsing_date = mbDate("+1 MONTH -1 DAY", "$match[1]-$match[2]-01");
  }
  
  // 130828
  if (preg_match("/(\d{2})(\d{2})(\d{2})/", $lapsing_date, $match)) {
    $lapsing_date = mbDate("+1 MONTH -1 DAY", "20$match[1]-$match[2]-01");
  }
}

$product = new CProduct();

if ($product_id)
  $product->load($product_id);
else if ($scc_code_part) {
  $product->scc_code = $scc_code_part;
  $product->loadMatchingObject();
}

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
  $dmi->_lot_number = $lot_number;
  $dmi->_lapsing_date = $lapsing_date;
  $dmi->_scc_code = $scc_code_part;
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
  
  if ($manufacturer_code) {
    $societe->manufacturer_code = $manufacturer_code;
    $societe->loadMatchingObject();
    $product->societe_id = $societe->_id;
  }
}

$lot = new CProductOrderItemReception();
$lot->code = $lot_number;
$lot->lapsing_date = $lapsing_date;

$smarty = new CSmartyDP();
$smarty->assign("list", $list_receptions);
$smarty->assign("dmi", $dmi);
$smarty->assign("list_references", $list_references);
$smarty->assign("list_societes", $list_societes);
$smarty->assign("product", $product);
$smarty->assign("keywords", $keywords);
$smarty->assign("scc_code_part", $scc_code_part);
$smarty->assign("lot", $lot);
$smarty->display('inc_search_product_order_item_reception.tpl');
