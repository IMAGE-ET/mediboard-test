<?php /* $Id: vw_idx_stock_location.php 7809 2010-01-12 15:05:03Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7809 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$stock_location_id = CValue::get('stock_location_id');

$stock_location = new CProductStockLocation();
$stock_location->load($stock_location_id);
$stock_location->loadRefsStocks();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('stock_location', $stock_location);
$smarty->display('print_stock_location.tpl');

?>