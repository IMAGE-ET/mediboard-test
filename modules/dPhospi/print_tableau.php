<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

$g        = CGroups::loadCurrent()->_id;
$date     = CValue::get("date"    , CMbDT::date());
$mode     = CValue::get("mode"    , 0);
$services_ids = CValue::getOrSession("services_ids", "");
$g        = CGroups::loadCurrent()->_id;

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids); 
}

if (!$services_ids) {
  $pref_services_ids = json_decode(CAppUI::pref("services_ids_hospi"));
  
  // Si la préférence existe, alors on la charge
  if (isset($pref_services_ids->{"g$g"})) {
    $services_ids = $pref_services_ids->{"g$g"};
    if ($services_ids) {
      $services_ids = explode("|", $services_ids); 
    }
    CValue::setSession("services_ids", $services_ids);
  }
  // Sinon, chargement de la liste des services en accord avec le droit de lecture
  else {
    $service = new CService();
    $where = array();
    $where["group_id"]  = "= '".$g."'";
    $where["cancelled"] = "= '0'";
    $services_ids = array_keys($service->loadListWithPerms(PERM_READ, $where, "externe, nom"));
    CValue::setSession("services_ids", $services_ids);
  }
}

// Récupération des chambres/services
$where = array();
$where["group_id"]   = "= '$g'";
$where["service_id"] = CSQLDataSource::prepareIn($services_ids);
$where["cancelled"]  = "= '0'";
$service = new CService();
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ,$where, $order);

// Chargement de chaque services
foreach ($services as $_service) {
  loadServiceComplet($_service, $date, $mode);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"    , $date);
$smarty->assign("demain"  , CMbDT::date("+ 1 day", $date));
$smarty->assign("services", $services);

$smarty->display("print_tableau.tpl");
