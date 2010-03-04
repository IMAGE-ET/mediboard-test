<?php /* $Id: httpreq_vw_products_list.php 8116 2010-02-22 11:37:54Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8116 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$endowment_id = CValue::getOrSession('endowment_id');
$start       = intval(CValue::getOrSession('start'));
$keywords    = CValue::getOrSession('keywords');
$letter      = CValue::getOrSession('letter', "%");

$where = array(
  "name" => ($letter === "#" ? "RLIKE '^[^A-Z]'" : "LIKE '$letter%'")
);

$endowment = new CProductEndowment();
$endowment->load($endowment_id);
$list = $endowment->seek($keywords, $where, "$start,20", true);
$total = $endowment->_totalSeek;

foreach($list as $_item) {
	$_item->loadRefs();
  $_item->countBackRefs("endowment_items");
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list', $list);
$smarty->assign('total', $total);
$smarty->assign('start', $start);
$smarty->assign('endowment', $endowment);
$smarty->assign('letter', $letter);

$smarty->display('inc_endowments_list.tpl');
