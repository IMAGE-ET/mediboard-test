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

$endowment_id = CValue::getOrSession('endowment_id');
$start       = intval(CValue::getOrSession('start'));
$keywords    = CValue::getOrSession('keywords');
$letter      = CValue::getOrSession('letter', "%");

$group_id = CGroups::loadCurrent()->_id;

$where = array(
  "product_endowment.name" => ($letter === "#" ? "RLIKE '^[^A-Z]'" : "LIKE '$letter%'"),
  "service.group_id"       => "= '$group_id'",
);

$ljoin = array(
  "service" => "service.service_id = product_endowment.service_id",
);

$endowment = new CProductEndowment();
$endowment->load($endowment_id);

$list = $endowment->seek($keywords, $where, "$start,25", true, $ljoin, "service.nom, product_endowment.name");
$total = $endowment->_totalSeek;

foreach ($list as $_item) {
  //$_item->loadRefs();
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
