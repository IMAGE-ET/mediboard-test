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

$regleSector = new CRegleSectorisation();

$showinactive = CValue::getOrSession("inactive", 0);

$where = array();

$where["group_id"] = " ='".CGroups::loadCurrent()->_id."'";
if (!$showinactive) {
  $where["date_max"] = " > '".CMbDT::dateTime()."' OR date_max IS NULL";
  $where["date_min"] = " < '".CMbDT::dateTime()."' OR date_min IS NULL";
}

$order = "priority DESC, praticien_id, function_id";
$regles = $regleSector->loadList($where, $order);

//mass load
CStoredObject::massLoadFwdRef($regles, "praticien_id");
CStoredObject::massLoadFwdRef($regles, "service_id");
CStoredObject::massLoadFwdRef($regles, "function_id");

$max_prio = 0;
/**
 * @var CRegleSectorisation $_regle
 */
foreach ($regles as $_regle) {
  $max_prio = ($_regle->priority > $max_prio) ? $_regle->priority : $max_prio;
  $_regle->loadRefPraticien()->loadRefFunction();
  $_regle->loadRefService();
  $_regle->loadRefFunction();
  $_regle->checkOlder();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("regles", $regles);
$smarty->assign("max_prio", $max_prio);
$smarty->assign("show_inactive", $showinactive);
$smarty->display("vw_sectorisations.tpl");