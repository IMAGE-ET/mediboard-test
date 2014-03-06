<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Selection de la date
$today = CMbDT::date();
$date  = CValue::getOrSession("date", $today);

$group = CGroups::loadCurrent();
$imagerie_etendue = CAppUI::conf("dPurgences CRPU imagerie_etendue", $group);

// Chargement des urgences prises en charge
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
  
$where["sejour.entree"] = "LIKE '$date%'";
$where["type"] = "= 'urg'";
if ($imagerie_etendue) {
  $ljoin["patients"] = "sejour.patient_id = patients.patient_id";
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["service"] = "affectation.service_id = service.service_id";
  $where[] = "(service.radiologie = '1') OR (rpu.bio_depart IS NOT NULL) OR (rpu.specia_att IS NOT NULL)";
}
else {
  $where[] = "(rpu.radio_debut IS NOT NULL) OR (rpu.bio_depart IS NOT NULL) OR (rpu.specia_att IS NOT NULL)";
}

$where["sejour.group_id"] = "= '$group->_id'";

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, null, null, null, $ljoin);

foreach ($listSejours as &$_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->_ref_rpu->loadRefSejourMutation();
  $_sejour->loadNDA();
  CMbObject::massLoadFwdRef($_sejour->loadRefsAffectations("sortie ASC"), "service_id");
  foreach ($_sejour->_ref_affectations as $_affectation) {
    $_affectation->loadRefService();
  }
  
  // Chargement de l'IPP
  $_sejour->_ref_patient->loadIPP();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listSejours"     , $listSejours);
$smarty->assign("date"            , $date);
$smarty->assign("today"           , $today);
$smarty->assign("imagerie_etendue", $imagerie_etendue);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_attente.tpl");
