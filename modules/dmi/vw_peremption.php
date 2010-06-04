<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$ljoin = array(
  "product_order_item" => "product_order_item.order_item_id = product_order_item_reception.order_item_id",
  "product_reference"  => "product_reference.reference_id = product_order_item.reference_id",
  "product"            => "product.product_id = product_reference.product_id",
);

$where = array(
  "product.product_id" => "IS NOT NULL",
  "product.code" => "IS NOT NULL",
  "product_reference.reference_id" => "IS NOT NULL",
);

$reception = new CProductOrderItemReception;
$receptions = $reception->loadList($where, "lapsing_date", 50, null, $ljoin);

foreach($receptions as $_id => $_reception) {
  $qty = $_reception->getQuantity();
  $_reception->_total_quantity = $qty;
  $_reception->_used_quantity = $_reception->countBackRefs('lines_dmi');
  $_reception->_remaining_quantity = $qty - $_reception->_used_quantity;
  if ($_reception->_remaining_quantity < 1) {
    unset($receptions[$_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receptions", $receptions);
$smarty->display("vw_peremption.tpl");
