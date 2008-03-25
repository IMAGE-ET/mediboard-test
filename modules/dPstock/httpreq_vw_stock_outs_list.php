<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

$order_by = 'date DESC';
$stock_out = new CProductStockOut();

$list_latest_stock_outs = $stock_out->loadList(null, $order_by, 20);

/*foreach($list_latest_stock_outs as $sto) {
  $sto->loadRefsFwd();
}*/

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_latest_stock_outs',  $list_latest_stock_outs);

$smarty->display('inc_stock_outs_list.tpl');

?>