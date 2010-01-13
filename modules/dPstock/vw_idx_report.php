<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsRead();

$list_stocks = new CProductStockGroup();

$where = array();
$where['group_id'] = " = '$g'";
$orderby = "quantity / order_threshold_min";
$list_stocks = $list_stocks->loadList($where, $orderby, 40);
foreach($list_stocks as $stock) {
	$stock->loadRefOrders();
}

$colors = array('#F00', '#FC3', '#1D6', '#06F', '#000');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $list_stocks);
$smarty->assign('colors', $colors);

$smarty->display('vw_idx_report.tpl');

?>