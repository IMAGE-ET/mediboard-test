<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$type     = mbGetValueFromGetOrSession('type');
$keywords = mbGetValueFromGetOrSession('keywords');

$order = new CProductOrder();
$societe = new CSociete();

if ($type != 'waiting' && 
    $type != 'pending' && 
    $type != 'old') {
	$type = 'waiting';
}

// we choose if it's an order that hasn't been sent yet
$neg = ($type == 'waiting')?'':'NOT ';

// the sql query (too complex to use the normal way)
$sql = "SELECT `product_order`.* FROM `product_order`, `societe` 
WHERE `product_order`.`date_ordered` IS $neg NULL 
AND `product_order`.`societe_id` = `societe`.`societe_id`";

// if keywords have been provided
if ($keywords) {
	$sql .= ' AND (';
	
	// we seek among ths orders
	$seeks = $order->getSeeks();
	foreach($seeks as $col => $comp) {
	  $sql .= "`product_order`.`$col` $comp '%$keywords%' OR";
	}
	
	// we seek among the societes
  $seeks = $societe->getSeeks();
  foreach($seeks as $col => $comp) {
    $sql .= "`societe`.`$col` $comp '%$keywords%' OR";
  }
	
	$sql .= ' 0)';
}

// we sort ths results
$sql .= 'ORDER BY `product_order`.`date_ordered` DESC';

$orders = $order->loadQueryList($sql);

// and we apply the last filters (not using sql)
$orders_filtered = array();
foreach($orders as $ord) {
  $ord->loadRefsFwd();
  $ord->updateFormFields();
  
  if ($type == 'waiting' ||
      $type == 'pending' && !$ord->_received ||
      $type == 'old'     &&  $ord->_received)	{
  	$orders_filtered[] = $ord;
  }
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('orders', $orders_filtered);
$smarty->assign('type',   $type);

$smarty->display('inc_orders_list.tpl');
?>
