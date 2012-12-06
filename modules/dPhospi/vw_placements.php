<?php /* $Id: vw_placements.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$services_ids = CValue::getOrSession("services_ids");
$group_id     = CValue::get("g");
$readonly     = CValue::getOrSession("readonly");

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

// Détection du changement d'établissement
if (!$services_ids || $group_id) {
  $group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;
  
  $pref_services_ids = json_decode(CAppUI::pref("services_ids_hospi"));
  
  // Si la préférence existe, alors on la charge
  if (isset($pref_services_ids->{"g$group_id"})) {
    $services_ids = $pref_services_ids->{"g$group_id"};
    if ($services_ids) {
      $services_ids = explode("|", $services_ids); 
    }
    CValue::setSession("services_ids", $services_ids);
  }
  // Sinon, chargement de la liste des services en accord avec le droit de lecture
  else {
    $service = new CService();
    $where = array();
    $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
    $where["cancelled"] = "= '0'";
    $services_ids = array_keys($service->loadListWithPerms(PERM_READ, $where, "externe, nom"));
    CValue::setSession("services_ids", $services_ids);
  }
}

$smarty = new CSmartyDP;

$smarty->assign("readonly", $readonly);

$smarty->display("vw_placements.tpl");
?>