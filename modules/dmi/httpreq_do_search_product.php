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

$code = CValue::get('code');

$parts = explode(" ", $code);
CMbArray::removeValue("", $parts);

$product_code = null;
$lot_code = null;

switch (count($parts)) {
  case 0: 
    break;
  case 1:
  	$product_code = $parts[0]; 
    break;
  case 2:
    $product_code = $parts[0];
    $lot_code = $parts[1];
    break;
}

if (!$product_code) {
  CAppUI::stepAjax("Veuillez indiquer un code lot", UI_MSG_ERROR);
}

//chargement produit
$product = new CProduct();
$product->code = $product_code;
$product->loadMatchingObject();

if(!$product->_id) {
  CAppUI::stepAjax("Produit de code <strong>$product_code</strong> non trouvé", UI_MSG_ERROR);
}
else {
	//chargement de la reception
  $where = array(
    "product.code" => "= '$product_code'",
    "product_order_item_reception.code" => "= '$lot_code'"
  );
  
  $leftjoin = array(
    "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
    "product_reference" => "product_order_item.reference_id = product_reference.reference_id",
    "product" => "product_reference.product_id=product.product_id"
  );
  
  $product_order_item_reception = new CProductOrderItemReception();
  $product_order_item_reception->loadObject($where, null, null, $leftjoin);
}
	
if (!$product_order_item_reception->_id) {
  CAppUI::stepAjax("Produit de code <strong>$product_code</strong> trouvé", UI_MSG_OK);
  CAppUI::stepAjax("Lot <strong>$lot_code</strong> non trouvé", UI_MSG_ERROR);
}
else {
  /*
	//chargement de la delivrance et de sa dispensation si elles existent
	$deliveryTrace = new CProductDeliveryTrace();
	$deliveryTrace->_ref_delivery_ref_stock->product_id = $product_code;
	$list_delivery = $deliveryTrace->loadMatchingList();
	
	//vérification des quantité encore disponibles en fonction du type de produit (usage unique, renouvelable)
	$quantite_suffisante = ((count($list_delivery)+1) <= $product_order_item_reception->quantity);
	
	if($product->_unique_usage && !$quantite_suffisante)//produit à usage unique déjà consommé; on bloque
	{
		CAppUI::stepAjax("Produit[".$product_code.";".$lot_code."] à usage unique déjà dispensé.",UI_MSG_ERROR);
		return false;
	}
  
	if(!$product->_unique_usage && $product->renewable==0)//produit consammable en quantité insuffisante
	  CAppUI::stepAjax("Produit[".$product_code.";".$lot_code."] entièrement consommé.",UI_MSG_ERROR);
  elseif($product->renewable==2) //produit renouvelable déjà consommé
    CAppUI::stepAjax("Produit[".$product_code.";".$lot_code."] renouvelable déjà consommé.",UI_MSG_ERROR);
  */
  
  $dmi = new CDMI();
  $dmi->code = $product_code;
  $dmi->loadMatchingObject();
  $product->_dmi_type = $dmi->type;
  
  $prescription_line_dmi = new CPrescriptionLineDMI;
  $prescription_line_dmi->quantity = 1;
    
  $smarty = new CSmartyDP();
  $smarty->assign('product', $product);
  $smarty->assign('product_order_item_reception', $product_order_item_reception);
  $smarty->assign('prescription_line_dmi', $prescription_line_dmi);
  //$smarty->assign('quantite_delivree',count($list_delivery));
  $smarty->display('inc_search_product.tpl');
}