<?php /* $Id: vw_mouvements.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

ini_set("memory_limit", "256M");

$services_ids = CValue::getOrSession("services_ids", null);
$granularite  = CValue::getOrSession("granularite", "day");
$date         = CValue::getOrSession("date", mbDate());
$granularites = array("day", "week", "4weeks");
$triAdm       = CValue::getOrSession("triAdm", "praticien");
$vue          = CValue::getOrSession("vue", "classique");

// Chargement des services pour la vue
$service = new CService;
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "externe, nom";
$all_services = $service->loadList($where, $order);
$services_allowed = $service->loadListWithPerms(PERM_READ, $where, $order);

if (!$services_ids) {
  $services_ids = array_keys($services_allowed);
}

$smarty = new CSmartyDP;

$smarty->assign("all_services", $all_services);
$smarty->assign("services_allowed", $services_allowed);
$smarty->assign("services_ids", $services_ids);
$smarty->assign("date"        , $date);
$smarty->assign("granularites", $granularites);
$smarty->assign("granularite" , $granularite);
$smarty->assign("vue"         , $vue);

$smarty->display("vw_mouvements.tpl");
?>