<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

$category_id = CValue::get('category_id');
$societe_id  = CValue::get('societe_id');
$keywords    = CValue::get('keywords');
$order_id    = CValue::get('order_id');
$limit       = CValue::get('limit');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($societe_id) {
  $where['product_reference.societe_id'] = " = $societe_id";
}
if ($keywords) {
  $where[] = "product_reference.code LIKE '%$keywords%' OR 
              product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$orderby = 'product.name ASC';

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_reference.product_id';

$reference = new CProductReference();
$list_references_count = $reference->countList($where, $orderby, null, null, $leftjoin);
$list_references = $reference->loadList($where, $orderby, $limit?$limit:20, null, $leftjoin);
foreach($list_references as $ref) {
  $ref->loadRefs();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_references',       $list_references);
$smarty->assign('list_references_count', $list_references_count);
$smarty->assign('order_id',              $order_id);

$smarty->display('inc_references_list.tpl');
?>
