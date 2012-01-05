<?php /* $Id: $ */

/**
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$services_ids = CValue::getOrSession("services_ids");
$view         = CValue::get("view");

$service = new CService;
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "externe, nom";
$all_services = $service->loadList($where, $order);
$services_allowed = $service->loadListWithPerms(PERM_READ, $where, $order);

$services_ids_hospi = CAppUI::pref("services_ids_hospi");

if (!$services_ids_hospi) {
  $services_ids_hospi = "{}";
}

$smarty = new CSmartyDP;

$smarty->assign("view"        , $view);
$smarty->assign("services_ids_hospi", $services_ids_hospi);
$smarty->assign("services_ids", $services_ids);
$smarty->assign("all_services", $all_services);
$smarty->assign("services_allowed", $services_allowed);
$smarty->assign("group_id", CGroups::loadCurrent()->_id);

$smarty->display("inc_select_services.tpl");

?>