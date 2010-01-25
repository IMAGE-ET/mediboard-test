<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CProductOrderItem');

if(CValue::post("_create_order")) {
  $reference_id = CValue::post("reference_id");
  $reference = new CProductReference;
  $reference->reference_id = $reference_id;
  
  if (!$reference_id || !$reference->loadMatchingObject()) {
    CAppUI::setMsg("Impossible de créer l'article, la réference n'existe pas", UI_MSG_ERROR);
  }
  
	$where = array(
	  "societe_id" => "= '$reference->societe_id'",
	);
  
  if (CAppUI::conf("dPstock group_independent") == 0) {
    $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
  }
	
	$order = new CProductOrder;
	$orders = $order->search("waiting", null, 1, $where);

	if (count($orders) == 0) {
    $order->societe_id = $reference->societe_id;
    $order->group_id = CGroups::loadCurrent()->_id;
    
    if ($msg = $order->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
    
		$order->order_number = $order->getUniqueNumber();
    
    if ($msg = $order->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
	}
	
	else {
		$order = reset($orders);
	}
  
	$_POST["order_id"] = $order->_id;
}

$do->doIt();
