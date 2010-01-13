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
$order_form  = CValue::get('order_form');
$start       = CValue::get('start', 0);

CValue::setSession('category_id', $category_id);
CValue::setSession('societe_id', $societe_id);
CValue::setSession('keywords', $keywords);

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
$total = $reference->countList($where, null, null, null, $leftjoin);
$list_references = $reference->loadList($where, $orderby, intval($start).",15", null, $leftjoin);
foreach($list_references as $ref) {
  $ref->loadRefsFwd();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_references', $list_references);
$smarty->assign('total', $total);
$smarty->assign('order_form', $order_form);
$smarty->assign('start', $start);

$smarty->display('inc_references_list.tpl');
