<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author Alexis Granger
 */

$service_id = mbGetValueFromGetOrSession('service_id');

$list_stocks_service = new CProductStockService();
$list_stocks_service = $list_stocks_service->loadList(array('service_id' => " = '$service_id'"));

foreach ($list_stocks_service as $stock) {
	$stock->loadRefsFwd();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('list_stocks_service'  , $list_stocks_service);
$smarty->display('inc_stocks_service_list.tpl');

?>