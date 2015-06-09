<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$current_user = CMediusers::get();

$keywords         = CValue::post("_view");
$all_departements = CValue::post("all_departements", 0);
$function_id      = CValue::get("function_id", $current_user->function_id);

if ($keywords == "") {
  $keywords = "%%";
}

$medecin = new CMedecin();
$order   = 'nom';
$group   = CGroups::loadCurrent();

$where = array();
$medecin_cps_prefs = CAppUI::pref("medecin_cps_pref");

$where["actif"] = "= '1'";

if ($medecin_cps_prefs != "") {
  $cps = preg_split("/\s*[\s\|,]\s*/", $medecin_cps_prefs);
  CMbArray::removeValue("", $cps);

  if (count($cps)) {
    $where_cp = array();
    foreach ($cps as $cp) {
      $where_cp[] = "cp LIKE '".$cp."%'";
    }
    $where[] = "(".implode(" OR ", $where_cp).")";
  }
}
else if ($group->_cp_court && !$all_departements) {
  $where['cp'] = "LIKE '".$group->_cp_court."%'";
}

$is_admin = $current_user->isAdmin();
if (CAppUI::conf('dPpatients CPatient function_distinct')) {
  $where["function_id"] = "= '$function_id'";
}

$matches = $medecin->seek($keywords, $where, 50, null, null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("keywords", $keywords);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_medecins_autocomplete.tpl");

