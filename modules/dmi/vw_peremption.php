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
  "product_order_item" => "product_order_item.order_item_id = product_order_item.order_item_id",
  "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
  "product"            => "product_reference.product_id = product.product_id",
  "dmi"                => "product.code = dmi.code",
);

$where = array(
  "product.code" => "IS NOT NULL"
);

$reception = new CProductOrderItemReception;
$receptions = $reception->loadList($where, "lapsing_date", null, null, $ljoin);

foreach($receptions as $_id => $_reception) {
  $_reception->_total_quantity = $_reception->getQuantity();
  $_reception->_used_quantity = $_reception->countBackRefs('lines_dmi');
  $_reception->_remaining_quantity = $_reception->getQuantity() - $_reception->_used_quantity;
  if ($_reception->_remaining_quantity < 1) {
    unset($receptions[$_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("receptions", $receptions);
$smarty->display("vw_peremption.tpl");
