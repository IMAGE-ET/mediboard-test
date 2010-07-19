<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$lot_id = CValue::get('lot_id');
$prescription_id = CValue::get('prescription_id');
$operation_id = CValue::get('operation_id');

$lot = new CProductOrderItemReception;
$lot->load($lot_id);

$lot->loadRefOrderItem()->loadReference()->loadRefProduct();
$product = $lot->_ref_order_item->_ref_reference->_ref_product;

$dmi = new CDMI;
$dmi->code = $product->code;
$dmi->loadMatchingObject();

$smarty = new CSmartyDP();
$smarty->assign("lot", $lot);
$smarty->assign("product", $product);
$smarty->assign("dmi", $dmi);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("operation_id", $operation_id);
$smarty->display("inc_apply_dmi.tpl");