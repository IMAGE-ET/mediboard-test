<?php /* $Id: httpreq_vw_products_list.php 8116 2010-02-22 11:37:54Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8116 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$selection_id = CValue::getOrSession('selection_id');
$start       = intval(CValue::getOrSession('start'));
$keywords    = CValue::getOrSession('keywords');
$letter      = CValue::getOrSession('letter', "%");

$where = array(
  "name" => ($letter === "#" ? "RLIKE '^[^A-Z]'" : "LIKE '$letter%'")
);

$selection = new CProductSelection();
$selection->load($selection_id);
$list = $selection->seek($keywords, $where, "$start,20", true);
$total = $selection->_totalSeek;

foreach($list as $_item) {
	$_item->loadRefs();
  $_item->countBackRefs("selection_items");
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list', $list);
$smarty->assign('total', $total);
$smarty->assign('start', $start);
$smarty->assign('selection', $selection);
$smarty->assign('letter', $letter);

$smarty->display('inc_selections_list.tpl');
