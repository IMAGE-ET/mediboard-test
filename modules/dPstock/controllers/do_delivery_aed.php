<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

if (isset ($_POST['_code'])) {
  $stock = CProductStockGroup::getFromCode($_POST['_code']);
  if ($stock) {
    $_POST['stock_class'] = "CProductStockGroup";
    $_POST['stock_id'] = $stock->_id;
    $_POST['_code'] = null;
  }
}

if (isset ($_POST['product_id'])) {
  $product = new CProduct;
  $product->load($_POST['product_id']);

  if ($product->loadRefStock()->_id) {
    $_POST["stock_class"] = $product->_ref_stock_group->_class;
    $_POST["stock_id"] = $product->_ref_stock_group->_id;

    unset($_POST['product_id']);
  }
  else if (isset($_POST['manual'])) {
    $stock_group = new CProductStockGroup();
    $stock_group->product_id = $product->_id;
    $stock_group->group_id = CProductStockGroup::getHostGroup();
    $stock_group->quantity = $_POST["quantity"];
    $stock_group->order_threshold_min = $_POST["quantity"];
    CAppUI::displayMsg($stock_group->store(), "CProductStockGroup-msg-create");

    $_POST["stock_class"] = $stock_group->_class;
    $_POST["stock_id"] = $stock_group->_id;

    unset($_POST['product_id']);
  }
}

$do = new CDoObjectAddEdit('CProductDelivery');
$do->doIt();

