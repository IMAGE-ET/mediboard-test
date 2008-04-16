<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$category_id = mbGetValueFromGet('category_id');
$societe_id  = mbGetValueFromGet('societe_id');
$keywords    = mbGetValueFromGet('keywords');
$order_id    = mbGetValueFromGet('order_id');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($societe_id) {
  $where['product.societe_id'] = " = $societe_id";
}
if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$orderby = 'product.name ASC';

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_reference.product_id';

$reference = new CProductReference();
$list_references = $reference->loadList($where, $orderby, 20, null, $leftjoin);
foreach($list_references as $ref) {
  $ref->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_references', $list_references);
$smarty->assign('order_id', $order_id);

$smarty->display('inc_references_list.tpl');
?>
