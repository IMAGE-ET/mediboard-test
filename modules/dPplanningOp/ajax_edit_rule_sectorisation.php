<?php

/**
 *
 *
 * @category PlanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkAdmin();

$rule_id = CValue::get("rule_id");

$rule_sectorisation = new CRegleSectorisation();
$rule_sectorisation->load($rule_id);

//mediusers
$user = CMediusers::get();
$users = $user->loadListFromType(null, PERM_EDIT);

//functions
$function = new CFunctions();
$functions = $function->loadList();

//services
$service = new CService();
$services = $service->loadList();

//services
$group = new CGroups();
$groups = $group->loadList();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("rule", $rule_sectorisation);
$smarty->assign("user", $user);
$smarty->assign("users", $users);
$smarty->assign("functions", $functions);
$smarty->assign("services", $services);
$smarty->assign("groups", $groups);
$smarty->display("vw_edit_rule_sectorisation.tpl");