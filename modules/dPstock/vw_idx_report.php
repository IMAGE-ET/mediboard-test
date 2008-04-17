<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$stock = new CProductStock();

$where = array();
$where['group_id'] = " = '$g'";
$orderby = "quantity / order_threshold_max";
$list_stocks = $stock->loadList($where, $orderby);
foreach($list_stocks as $stock) {
	$stock->updateDBFields();
	$stock->loadRefOrders();
}

$colors = array('#F00', '#FC3', '#1D6', '#06F', '#000');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $list_stocks);
$smarty->assign('colors', $colors);

$smarty->display('vw_idx_report.tpl');

?>