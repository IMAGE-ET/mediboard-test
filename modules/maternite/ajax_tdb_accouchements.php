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

$op = new COperation();
$ljoin = array("sejour" => "sejour.sejour_id = operations.sejour_id");
$where = array(
  "sejour.grossesse_id" => " IS NOT NULL",
  "sejour.entree" => "<= '$date 23:59:59' "
);

$where[] = "(sejour.sortie >= '$date 00:00:00' OR sejour.sortie IS NULL)";

/** @var COperation[] $ops */
$ops = $op->loadList($where, "date, time_operation", null, null, $ljoin);

foreach ($ops as $_op) {
  $_op->loadRefChir()->loadRefFunction();
  $_op->loadRefAnesth();
  $_op->loadRefSalle();
  $sejour = $_op->loadRefSejour();
  $grossesse = $sejour->loadRefGrossesse();
  $grossesse->loadRefsNaissances();
  $grossesse->loadRefParturiente();

}

$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("ops", $ops);
$smarty->display("inc_tdb_accouchements.tpl");