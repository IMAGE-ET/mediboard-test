<?php 

/**
* @package Mediboard
* @subpackage dmi
* @version $Revision$
* @author Stéphanie Subilia
*/

global $AppUI, $can;
$can->needsRead();

$product_id = mbGetValueFromGet('product_id');

$product = new CProduct();
$product->load($product_id);

if(!$product->_id)
  CAppUI::stepAjax("Produit de code ".$product_id." non trouvé",UI_MSG_ERROR);
else
{
	$where = array();
	$where["product.product_id"] = "= $product_id";
  $leftjoin = array();
  $leftjoin["product_order_item"] = "product_order_item_reception.order_item_id = product_order_item.order_item_id";
  $leftjoin["product_reference"] = "product_order_item.reference_id = product_reference.reference_id";
  $leftjoin["product"] = "product_reference.product_id=product.product_id";
  
	
	
  //chargement de la reception
  $product_order_item_reception = new CProductOrderItemReception();
  $list = $product_order_item_reception->loadList($where,null,null,null,$leftjoin);
  foreach ($list as $_poir)
  {
  	$_poir->loadRefsFwd();
  }
}

if(count($list)>0)
{
	$smarty = new CSmartyDP();
	$smarty->assign("list",$list);
	$smarty->display('inc_search_product_order_item_reception.tpl');
}
else
{
	CAppUI::stepAjax("Pas de reception pour ce produit",UI_MSG_ERROR);
}

?>