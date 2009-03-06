<?php 

/**
* @package Mediboard
* @subpackage dmi
* @version $Revision:  $
* @author Stéphanie Subilia
*/

global $AppUI, $can, $m, $g;
$can->needsRead();

$product_code = mbGetValueFromGet('code');
$product_code_lot = mbGetValueFromGet('code_lot');

//chargement produit
$product = new CProduct();
$product->code = $product_code;
$product->loadMatchingObject();

if(!$product->_id)
  CAppUI::stepAjax("Produit de code ".$product_code." non trouvé",UI_MSG_ERROR);
else
{
	//chargement de la reception
  $where = array();
  $where["product.code"] = "= '$product_code'";
  $where["product_order_item_reception.code"] = "= '$product_code_lot'";
  $leftjoin = array();
  $leftjoin["product_order_item"] = "product_order_item_reception.order_item_id = product_order_item.order_item_id";
  $leftjoin["product_reference"] = "product_order_item.reference_id = product_reference.reference_id";
  $leftjoin["product"] = "product_reference.product_id=product.product_id";
  
  $product_order_item_reception = new CProductOrderItemReception();
  $product_order_item_reception->loadObject($where,null,null,$leftjoin);
}
	
  if(!$product_order_item_reception->_id)
    CAppUI::stepAjax("Produit de code".$product_code." trouvé; lot ".$product_code_lot."non trouvé",UI_MSG_ERROR);
  else{
    
    /*
  	//chargement de la delivrance et de sa dispensation si elles existent
  	$deliveryTrace = new CProductDeliveryTrace();
  	$deliveryTrace->_ref_delivery_ref_stock->product_id = $product_code;
  	$list_delivery = $deliveryTrace->loadMatchingList();
  	
  	//vérification des quantité encore disponibles en fonction du type de produit (usage unique, renouvelable)
  	$quantite_suffisante = ((count($list_delivery)+1) <= $product_order_item_reception->quantity);
		
  	if($product->_unique_usage && !$quantite_suffisante)//produit à usage unique déjà consommé; on bloque
  	{
  		CAppUI::stepAjax("Produit[".$product_code.";".$product_code_lot."] à usage unique déjà dispensé.",UI_MSG_ERROR);
  		return false;
  	}
    
  	if(!$product->_unique_usage && $product->renewable==0)//produit consammable en quantité insuffisante
  	  CAppUI::stepAjax("Produit[".$product_code.";".$product_code_lot."] entièrement consommé.",UI_MSG_ERROR);
    elseif($product->renewable==2) //produit renouvelable déjà consommé
      CAppUI::stepAjax("Produit[".$product_code.";".$product_code_lot."] renouvelable déjà consommé.",UI_MSG_ERROR);
    */
    
    $smarty = new CSmartyDP();
    $smarty->assign('product',$product);
    $smarty->assign('product_order_item_reception', $product_order_item_reception);
    $smarty->assign('quantite_delivrable',$product_order_item_reception->quantity);
    //$smarty->assign('quantite_delivree',count($list_delivery));
    $smarty->display('inc_search_product.tpl');}


?>