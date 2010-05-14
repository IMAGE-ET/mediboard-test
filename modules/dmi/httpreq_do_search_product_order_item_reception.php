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

$product = new CProduct();
$product->load($product_id);

if(!$product->_id) {
  CAppUI::stepAjax("Produit de code <strong>$product_id</strong> non trouvé", UI_MSG_ERROR);
}
else {
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
  $list = $product_order_item_reception->loadList($where,null,null,null,$leftjoin);
  foreach ($list as $_poir) {
  	$_poir->loadRefsFwd();
  }
}

if(count($list) > 0) {
	$smarty = new CSmartyDP();
	$smarty->assign("list",$list);
	$smarty->display('inc_search_product_order_item_reception.tpl');
}
else {
	CAppUI::stepAjax("Aucun article enregistré pour le produit <strong>$product->_view</strong>", UI_MSG_ERROR);
}
