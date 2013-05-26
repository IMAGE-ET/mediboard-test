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

CCanDo::checkRead();

$stock_id    = CValue::getOrSession('stock_id');
$stock_class = CValue::get('stock_class', 'CProductStockGroup');

// Loads the stock in function of the stock ID or the product ID
$stock = new $stock_class;
if ($stock_id) {
  $stock->stock_id = $stock_id;
  if ($stock->loadMatchingObject()) {
    $stock->loadRefsFwd();
  }
}

$list_services = CProductStockGroup::getServicesList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock', $stock);
$smarty->assign('list_services',  $list_services);

$smarty->display('inc_aed_delivery_stock_item.tpl');

