<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

mbTrace("permettre de mettre un numero de facture à un bon de commande");

CCanDo::checkRead();

$date = CValue::get("date", mbDate());
$tomorow = mbDate("+1 DAY", $date);

$dmi_line = new CPrescriptionLineDMI();
$dmi_line->date = $date;

$where = array();
$where["date"] = "BETWEEN '$date' AND '$tomorow'";

$list_lines = $dmi_line->loadList($where, "date");

$lines_by_context = array();
$contexts = array();

foreach($list_lines as $_dmi) {
  $_dmi->loadRefsFwd();
  $_dmi->_ref_prescription->loadRefPatient();
  $_dmi->_ref_product->loadBackRefs("references");
  $_dmi->_ref_product->loadRefStock();
  $_dmi->_ref_operation->loadRefsFwd();
  
  $product_order = new CProductOrder;
  $product_order->setObject($_dmi->_ref_operation);
  $orders = $product_order->loadMatchingList();
  
  foreach($orders as $_order_id => $_order) {
    $_order->loadRefsOrderItems();
    foreach($_order->_ref_order_items as $_order_item) {
      $_order_item->loadReference();
      if ($_order_item->_ref_reference->product_id != $_dmi->product_id) {
        unset($orders[$_order_id]);
        break;
      }
    }
  }
  
  $_dmi->_orders = $orders;
    
  foreach($_dmi->_ref_product->_back["references"] as $_reference) {
  	$_reference->loadRefSociete();
  }
  
  if (!isset($lines_by_context[$_dmi->_ref_prescription->_guid])) {
    $lines_by_context[$_dmi->_ref_prescription->_guid] = array();
    $contexts[$_dmi->_ref_prescription->_guid] = $_dmi->_ref_prescription;
  }
  $lines_by_context[$_dmi->_ref_prescription->_guid][$_dmi->_id] = $_dmi;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date"            , $date);
$smarty->assign("dmi_line"        , $dmi_line);
$smarty->assign("lines_by_context", $lines_by_context);
$smarty->assign("contexts"        , $contexts);
$smarty->display("vw_commandes.tpl");
