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

$merge = CView::get("merge", "bool default|0");
$auto  = CView::get("auto" , "bool default|0");
$max   = CView::get("max"  , "num default|10");
CView::checkin();

$group = CGroups::loadCurrent();
$plage = new CPlageOp();

$query = new CRequest();
$query->addColumn("GROUP_CONCAT( plageop_id  SEPARATOR '-')", "plage_ids");
$query->addColumn("COUNT( plageop_id )", "plage_count");
$query->addColumn("date"    ); $query->addGroup("date"   );
$query->addColumn("debut"   ); $query->addGroup("debut"  );
$query->addColumn("fin"     ); $query->addGroup("fin"    );
$query->addColumn("chir_id" ); $query->addGroup("chir_id");
$query->addColumn("spec_id" ); $query->addGroup("spec_id");
$query->addColumn("plagesop.salle_id"); $query->addGroup("plagesop.salle_id");
$query->addLJoinClause("sallesbloc", "sallesbloc.salle_id = plagesop.salle_id");
$query->addLJoinClause("bloc_operatoire", "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id");
$query->addWhereClause("bloc_operatoire.group_id", "= '$group->_id'");
$query->addOrder("plagesop.date");
$query->addHaving("plage_count > 1");

$ds = $plage->_spec->ds;
$duplicates = $ds->loadColumn($query->makeSelect($plage));
$count = count($duplicates);
$success_count = 0;
$failures = array();
$i = $max;
if ($merge) {
  foreach ($duplicates as $_plage_ids) {
    if (!$i--) {
      break;
    }

    $plage_ids = explode("-", $_plage_ids);
    $plages = $plage->loadAll($plage_ids);
    /** @var CPlageOp $first */
    $first = array_shift($plages);
    while ($next = array_shift($plages)) {
      if ($msg = $first->merge(array($next))) {
        $failures[$_plage_ids] = $msg;
        continue 2;
      }
    }

    $success_count++;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("merge", $merge);
$smarty->assign("auto" , $auto);
$smarty->assign("max"  , $max);
$smarty->assign("count", $count);
$smarty->assign("success_count", $success_count);
$smarty->assign("failures", $failures);

$smarty->display("merge_duplicate_plagesop.tpl");

