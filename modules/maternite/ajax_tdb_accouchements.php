<?php

/**
 * Liste des accouchements en cours du tableau de bord
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date  = CValue::get("date", CMbDT::date());
$date_min = CMbDT::date("-1 DAY", $date);
$group = CGroups::loadCurrent();

$op = new COperation();
$ljoin = array(
  "sejour" => "sejour.sejour_id = operations.sejour_id",
  "grossesse" => "sejour.grossesse_id = grossesse.grossesse_id");
$where = array(
  "sejour.grossesse_id" => " IS NOT NULL",
  "sejour.group_id" => " = '$group->_id' ",
  " date BETWEEN '$date_min' AND '$date' "
);


/** @var COperation[] $ops */
$ops = $op->loadList($where, "date DESC, time_operation", null, null, $ljoin);

CMbObject::massLoadFwdRef($ops, "sejour_id");
CMbObject::massLoadFwdRef($ops, "sejour_id");
CMbObject::massLoadFwdRef($ops, "anesth_id");
$chirs = CMbObject::massLoadFwdRef($ops, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");

foreach ($ops as $_op) {
  $_op->loadRefChir()->loadRefFunction();
  $_op->loadRefAnesth();
  $_op->loadRefSalle();
  $_op->loadRefPlageOp();
  $sejour = $_op->loadRefSejour();
  $grossesse = $sejour->loadRefGrossesse();
  $grossesse->loadRefsNaissances();
  $grossesse->loadRefParturiente();
  $_op->updateDatetimes();
}

$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("ops", $ops);
$smarty->display("inc_tdb_accouchements.tpl");