<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
$list = $selection->seek($keywords, $where, "$start,20", true, null, "name");
$total = $selection->_totalSeek;

foreach ($list as $_item) {
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
