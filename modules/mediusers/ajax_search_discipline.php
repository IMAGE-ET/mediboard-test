<?php

/**
 * View disciplines
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: vw_idx_functions.php 19463 2013-06-07 10:36:29Z lryo $
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$page   = intval(CValue::get('page'  , 0));
$filter = CValue::getOrSession("filter", "");

$step = 25;

$order = "text ASC";

$discipline = new CDiscipline();
if ($filter) {
  $disciplines       = $discipline->seek($filter, null, "$page, $step", true, null, $order);
  $total_disciplines = $discipline->_totalSeek;
}
else {
  $disciplines       = $discipline->loadList(null, $order, "$page, $step");
  $total_disciplines = $discipline->countList();
}

foreach ($disciplines as $_discipline) {
  $_discipline->loadGroupRefsBack();
}

$function_id = CValue::getOrSession("function_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("discipline"       , $discipline);
$smarty->assign("disciplines"      , $disciplines);
$smarty->assign("total_disciplines", $total_disciplines);
$smarty->assign("page"             , $page);
$smarty->assign("step"             , $step);

$smarty->display("vw_list_disciplines.tpl");
