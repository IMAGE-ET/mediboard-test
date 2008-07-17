<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

$stock_id = mbGetValueFromGetOrSession('stock_id');

// Loads the stock in function of the stock ID or the product ID
$stock = new CProductStockGroup();
if ($stock_id) {
  $stock->stock_id = $stock_id;
  if ($stock->loadMatchingObject()) {
	  $stock->loadRefsFwd();
	}
}

$function = new CFunctions();
$list_functions = $function->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('stock', $stock);
$smarty->assign('list_functions',  $list_functions);

$smarty->display('inc_aed_stock_out_stock_item.tpl');

?>