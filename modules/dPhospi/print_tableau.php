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
$date     = CValue::get("date"    , mbDate());
$mode     = CValue::get("mode"    , 0);
$services = CValue::getOrSession("services_ids", "");
$g        = CGroups::loadCurrent()->_id;

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$g'";
$where["service_id"] = CSQLDataSource::prepareIn($services);
$service = new CService;
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ,$where, $order);

// Chargement de chaque services
foreach ($services as $_service) {
  loadServiceComplet($_service, $date, $mode);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"    , $date);
$smarty->assign("demain"  , mbDate("+ 1 day", $date));
$smarty->assign("services", $services);

$smarty->display("print_tableau.tpl");

?>