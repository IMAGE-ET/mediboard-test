<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$operation = new COperation;

// Nombre d'interventions...
$counts["total"] = $operation->countList();

// Dans une plage...
$where["operations.plageop_id"] = "IS NOT NULL";
$counts["plaged"] = $operation->countList($where);

// Sans date!
$where["operations.date"] = "IS NULL";
$counts["missing"] = $operation->countList($where);

// Avec une date erronée!
$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
$where["operations.date"] = "IS NOT NULL";
$where[] = "plagesop.date != operations.date";
$counts["wrong"] = $operation->countList($where, null, $ljoin);

foreach ($operation->loadList($where, null, null, null, $ljoin) as $_operation) {
  mbTrace($_operation->_guid, $_operation->plageop_id);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("counts", $counts);
$smarty->display("check_date_intervention.tpl");
