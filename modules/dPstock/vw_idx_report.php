<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m, $g;

$can->needsRead();

$stock_id    = mbGetValueFromGetOrSession('stock_id');

// Loads the stock in function of the stock ID or the product ID
$stock = new CProductStock();

// If stock_id has been provided, we load the associated product
if ($stock_id) {
  $stock->stock_id = $stock_id;
  $stock->loadMatchingObject();
  $stock->loadRefsFwd();
  $stock->_ref_product->loadRefsFwd();
}

$where = array();
$where['group_id'] = " = '$g'";
$orderby = "quantity / order_threshold_max";
$list_stocks = $stock->loadList($where, $orderby);
foreach($list_stocks as $stock) {
	$stock->updateDBFields();
}

$colors = array('#F00', '#FC3', '#1D6', '#06F', '#000');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock',       $stock);
$smarty->assign('colors',      $colors);
$smarty->assign('list_stocks', $list_stocks);

$smarty->display('vw_idx_report.tpl');

?>