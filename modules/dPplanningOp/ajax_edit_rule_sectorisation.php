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

$rule_id  = CValue::get("rule_id");
$clone    = CValue::get("clone", 0);

$rule_sectorisation = new CRegleSectorisation();
$rule_sectorisation->load($rule_id);

if ($clone) {
  $rule_sectorisation->_id = null;
}

//mediusers
$user = CMediusers::get();
$users = $user->loadPraticiens(PERM_EDIT);

//functions
$function = new CFunctions();
$functions = $function->loadList(null, "text");

//services
$service = new CService();
$services = $service->loadList();

//services
$group = new CGroups();
$groups = $group->loadList();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("rule", $rule_sectorisation);
$smarty->assign("clone", $clone);
$smarty->assign("user", $user);
$smarty->assign("users", $users);
$smarty->assign("functions", $functions);
$smarty->assign("services", $services);
$smarty->assign("groups", $groups);
$smarty->display("vw_edit_rule_sectorisation.tpl");