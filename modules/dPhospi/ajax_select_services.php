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
$services_ids_suggest = CValue::get("services_ids_suggest", null);
$view         = CValue::get("view");
$ajax_request = CValue::get("ajax_request", 1);

if (!is_array($services_ids_suggest) && !is_null($services_ids_suggest)) {
  $services_ids = explode(",", $services_ids_suggest);
}

$group_id = CGroups::loadCurrent()->_id;

$service = new CService;
$where = array();
$where["group_id"] = "= '$group_id'";
$where["secteur_id"] = "IS NULL";
$order = "externe, nom";
$all_services = $service->loadList($where, $order);

unset($where["secteur_id"]);
$services_allowed = $service->loadListWithPerms(PERM_READ, $where, $order);

$where = array();
$where["group_id"] = "= '$group_id'";
$secteur = new CSecteur;
$secteurs = $secteur->loadList($where, "nom");

foreach ($secteurs as $_secteur) {
  $_secteur->loadRefsServices();
  $_secteur->_all_checked = count($_secteur->_ref_services) > 0 ?
    array_intersect(array_keys($_secteur->_ref_services), array_keys($services_ids)) === array_keys($_secteur->_ref_services) : false;
}

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
$smarty->assign("group_id"    , CGroups::loadCurrent()->_id);
$smarty->assign("secteurs"    , $secteurs);
$smarty->assign("ajax_request" , $ajax_request);

$smarty->display("inc_select_services.tpl");
