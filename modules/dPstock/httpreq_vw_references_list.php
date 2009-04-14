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

$category_id = mbGetValueFromGet('category_id');
$societe_id  = mbGetValueFromGet('societe_id');
$keywords    = mbGetValueFromGet('keywords');
$order_id    = mbGetValueFromGet('order_id');
$limit       = mbGetValueFromGet('limit');
$hide_societes = mbGetValueFromGet('hidden_column');

$where = array();
if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}
if ($societe_id) {
  $where['product_reference.societe_id'] = " = $societe_id";
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
$smarty->assign('hide_societes',         $hide_societes);

$smarty->display('inc_references_list.tpl');
?>
