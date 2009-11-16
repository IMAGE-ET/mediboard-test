<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// If it is a return to the group stock
if (isset ($_POST['_return']) && isset ($_POST['_code'])) {
  $stock_service = CProductStockService::getFromCode($_POST['_code']);
  $stock_group   = CProductStockGroup::getFromCode($_POST['_code']);
  
  $_POST['quantity'] = -abs($_POST['quantity']);
  $_POST['_code'] = null;
  $_POST['_return'] = null;
    
  if ($stock_service && $stock_group) {
  	$delivery = new CProductDelivery();
  	$where = array(
      'stock_id' => "= $stock_group->_id",
      'service_id' => "= $stock_service->service_id",
      'quantity' => "< 0"
    );
  	if (!$delivery->loadObject($where)) {
  		$delivery->stock_id = $stock_group->_id;
  		$delivery->service_id = $stock_service->service_id;
  	}
  	$delivery->quantity += $_POST['quantity'];
  	$delivery->date_dispensation = MbDateTime();
  	if ($msg = $delivery->store()) CAppUI::setMsg($msg, UI_MSG_ERROR);
  	$_POST['delivery_id'] = $delivery->_id;
  }
}

$do = new CDoObjectAddEdit('CProductDeliveryTrace', 'delivery_trace_id');
$do->doIt();

?>