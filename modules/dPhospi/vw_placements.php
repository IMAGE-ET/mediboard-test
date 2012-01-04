<?php /* $Id: vw_placements.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$services_ids = CValue::getOrSession("services_ids");

if (!$services_ids) {
  // Si la préférence existe, alors on la charge
  if ($pref_services_ids = CAppUI::pref("services_ids_hospi")) {
    $services_ids = explode("|", $pref_services_ids);
    CValue::setSession("services_ids", $services_ids);
  }
  // Sinon, chargement de la liste des services en accord avec le droit de lecture
  else {
    $service = new CService;
    $where = array();
    $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
    $services_allowed = $service->loadListWithPerms(PERM_READ, $where, "externe, nom");
    CValue::setSession("services_ids", array_keys($services_allowed));
  }
}

$smarty = new CSmartyDP;

$smarty->display("vw_placements.tpl");
?>