<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$stock_location_id = mbGetValueFromGetOrSession('stock_location_id');

$stock_location = new CProductStockLocation();
$stock_location->load($stock_location_id);
$list_locations = $stock_location->loadList(null, 'position,name');

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('stock_location', $stock_location);
$smarty->assign('list_locations', $list_locations);
$smarty->display('vw_idx_stock_location.tpl');

?>