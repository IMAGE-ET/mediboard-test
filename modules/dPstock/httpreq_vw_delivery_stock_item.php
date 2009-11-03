<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsRead();

$stock_id = CValue::getOrSession('stock_id');

// Loads the stock in function of the stock ID or the product ID
$stock = new CProductStockGroup();
if ($stock_id) {
  $stock->stock_id = $stock_id;
  if ($stock->loadMatchingObject()) {
	  $stock->loadRefsFwd();
	}
}

$service = new CService();
$list_services = $service->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock', $stock);
$smarty->assign('list_services',  $list_services);

$smarty->display('inc_aed_delivery_stock_item.tpl');

?>