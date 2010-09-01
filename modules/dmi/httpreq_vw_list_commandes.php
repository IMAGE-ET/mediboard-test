<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::get("date");
$type = CValue::get("type");

CValue::setSession("date", $date);
CValue::setSession("type", $type);

$date_min = $date;
$date_max = mbDate("+1 DAY", mbDate());

// ancien code
$dmi_line = new CPrescriptionLineDMI();

$where = array();
$where["date"] = "BETWEEN '$date_min' AND '$date_max'";

if ($type) {
  $where["type"] = " = '$type'";
}

$list_lines = $dmi_line->loadList($where, "date");

$lines_by_context = array();
$contexts = array();
$intervs = array();
$list_order_items = array();

foreach($list_lines as $_dmi) {
  $_dmi->loadRefsFwd();
  $_dmi->_ref_prescription->loadRefPatient();
  $_dmi->_ref_product->loadBackRefs("references");
  $_dmi->_ref_product->loadRefStock();
  $_dmi->_ref_operation->loadRefsFwd();
  $_dmi->_ref_product_order_item_reception->loadRefOrderItem();
  
  $_dmi->_remaining_qty = $_dmi->quantity;
  
  // chargement de toutes les lignes de commande de l'interv
  if (!isset($list_order_items[$_dmi->operation_id])) {
    $list_order_items[$_dmi->operation_id] = array();
    
    $product_order = new CProductOrder;
    $product_order->setObject($_dmi->_ref_operation);
    $orders = $product_order->loadMatchingList();
    
    foreach($orders as $_order_id => $_order) {
      $_order->loadRefsOrderItems();
      foreach($_order->_ref_order_items as $_order_item) {
        $_order_item->loadOrder();
      }
      
      $list_order_items[$_dmi->operation_id] += $_order->_ref_order_items;
    }
  }
  
  $dmi_renewal = ($_dmi->type == "deposit" ? "1" : "0");
  
  // ajoute des lignes de commandes aux lignes de dmi
  $_dmi->_order_items = array();
  
  if ($list_order_items[$_dmi->operation_id]) {
    foreach($list_order_items[$_dmi->operation_id] as $_order_item_id => $_order_item) {
      // si la ligne de commande correspond a la ligne de dmi
      if ($_order_item->lot_id == $_dmi->order_item_reception_id &&
          $_order_item->septic == $_dmi->septic &&
          $_order_item->renewal == $dmi_renewal) {
            
        // on soustrait la quantité du dmi a la ligne pour dire qu'elle est "deja prise" par la ligne de dmi
        $qty = min($_order_item->quantity, $_dmi->quantity);
        $_order_item->quantity -= $qty;
        $_dmi->_remaining_qty -= $qty;
        
        $_dmi->_order_items[$_order_item->_id] = $_order_item;
        
        // si la ligne de commande est epuisée, on la supprime de la liste
        if ($_order_item->quantity == 0) {
          unset($list_order_items[$_dmi->operation_id][$_order_item_id]);
        }
        if ($_dmi->_remaining_qty == 0) {
          break;
        }
      }
    }
      
    if ($_dmi->_remaining_qty > 0) {
      $_dmi->_order_items = array();
    }
  }
  
  $_dmi->_new_order_item = new CProductOrderItem;
  $_dmi->_new_order_item->renewal = $dmi_renewal;
    
  foreach($_dmi->_ref_product->_back["references"] as $_reference) {
  	$_reference->loadRefSociete();
  }
  
  $intervs[$_dmi->_ref_prescription->_guid] = $_dmi->_ref_operation;
    
  if (!isset($lines_by_context[$_dmi->_ref_prescription->_guid])) {
    $lines_by_context[$_dmi->_ref_prescription->_guid] = array();
    $contexts[$_dmi->_ref_prescription->_guid] = $_dmi->_ref_prescription;
  }
  $lines_by_context[$_dmi->_ref_prescription->_guid][$_dmi->_id] = $_dmi;
}



/*$interv = new COperation;
$where = array(
  "operations.date BETWEEN '$date_min' AND '$date_max' OR
   plagesop.date BETWEEN '$date_min' AND '$date_max'"
);

$ljoin = array(
  "prescription_line_dmi" => "prescription_line_dmi.operation_id = operations.operation_id",
  "plagesop" => "plagesop.plageop_id = operations.plageop_id",
);

$list_intervs = $interv->loadList($where, "operations.date, plagesop.date", null, null, $ljoin);
*/


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines_by_context", $lines_by_context);
$smarty->assign("contexts"        , $contexts);
$smarty->assign("intervs"        , $intervs);
$smarty->display("inc_list_commandes.tpl");
