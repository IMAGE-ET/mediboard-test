<?php
/**
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$purge = CView::get("purge", "bool default|0");
$auto  = CView::get("auto" , "bool default|0");
$max   = CView::get("max"  , "num default|100");
CView::checkin();

$group = CGroups::loadCurrent();
$ljoin["operations"] = "plagesop.plageop_id = operations.plageop_id";
$ljoin["sallesbloc"] = "sallesbloc.salle_id = plagesop.salle_id";
$ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";
$where["operations.operation_id"] = "IS NULL";
$where["bloc_operatoire.group_id"] = "= '$group->_id'";
$order = "plagesop.date";

$plage = new CPlageOp();
$success_count = 0;
$failures = array();
$plages = array();
if ($purge) {
  /** @var CPlageOp[] $plages */
  $plages = $plage->loadList($where, $order, $max, null, $ljoin);
  foreach ($plages as $_plage) {

    // Suppression des affectationde personnel
    foreach ($_plage->loadAffectationsPersonnel() as $_affectations) {
      foreach ($_affectations as $_affectation) {
        $_affectation->delete();
      }
    }

    if ($msg = $_plage->delete()) {
      $failures[$_plage->_id] = $msg;
      $_plage->loadRefSalle();
      continue;
    }

    $success_count++ ;
  }
}

$count = $plage->countList($where, null, $ljoin);

$smarty = new CSmartyDP;

$smarty->assign("plages", $plages);
$smarty->assign("purge", $purge);
$smarty->assign("max"  , $max);
$smarty->assign("auto" , $auto);
$smarty->assign("count", $count);
$smarty->assign("success_count", $success_count);
$smarty->assign("failures", $failures);

$smarty->display("purge_empty_plagesop.tpl");

