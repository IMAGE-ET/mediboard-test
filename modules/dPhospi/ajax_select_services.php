<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$services_ids         = CValue::getOrSession("services_ids", array());
$services_ids_suggest = CValue::get("services_ids_suggest", null);
$view                 = CValue::get("view");
$ajax_request         = CValue::get("ajax_request", 1);

if (!is_array($services_ids_suggest) && !is_null($services_ids_suggest)) {
  $services_ids = explode(",", $services_ids_suggest);
}

$group_id = CGroups::loadCurrent()->_id;

$where               = array();
$where["group_id"]   = "= '$group_id'";
$where["cancelled"]  = "= '0'";
$where["secteur_id"] = "IS NULL";
$order               = "externe, nom";

$service = new CService();
$all_services        = $service->loadList($where, $order);

unset($where["secteur_id"]);
$services_allowed = $service->loadListWithPerms(PERM_READ, $where, $order);

$where = array();
$where["group_id"] = "= '$group_id'";

$secteur  = new CSecteur();
$secteurs = $secteur->loadList($where, "nom");

foreach ($secteurs as $_secteur) {
  $_secteur->loadRefsServices();
  $keys2 = array_keys($_secteur->_ref_services);
  $_secteur->_all_checked = count($_secteur->_ref_services) > 0 ?
    array_values(array_intersect($services_ids, $keys2)) == $keys2 : false;
}

$services_ids_hospi = CAppUI::pref("services_ids_hospi");

if (!$services_ids_hospi) {
  $services_ids_hospi = "{}";
}

$smarty = new CSmartyDP("modules/dPhospi");

$smarty->assign("view"        , $view);
$smarty->assign("services_ids_hospi", $services_ids_hospi);
$smarty->assign("services_ids", $services_ids);
$smarty->assign("all_services", $all_services);
$smarty->assign("services_allowed", $services_allowed);
$smarty->assign("group_id"    , CGroups::loadCurrent()->_id);
$smarty->assign("secteurs"    , $secteurs);
$smarty->assign("ajax_request" , $ajax_request);

$smarty->display("inc_select_services.tpl");
