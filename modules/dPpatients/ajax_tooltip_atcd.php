<?php
/**
 * Tooltip des antécédents du patient
 *
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$dossier_medical_id = CValue::get("dossier_medical_id");
$object_guid         = CValue::get("object_guid");
$type               = CValue::get("type");
$exclude            = CValue::get("exclude");

if ($object_guid) {
  $dossier_medical = CMbObject::loadFromGuid($object_guid);
}
else {
  $dossier_medical = new CDossierMedical();
  $dossier_medical->load($dossier_medical_id);
}

if ($type) {
  $dossier_medical->loadRefsAntecedentsOfType($type);
}
else {
  $dossier_medical->loadRefsAntecedents();
}

$tab_atc = array();
$ant_communs = array();

$patient = null;
if ($dossier_medical->object_class == "CSejour") {
  $dossier_medical->loadRefObject();
  /* @var CSejour $sejour*/
  $sejour = $dossier_medical->_ref_object;
  $doss_patient = $sejour->loadRefPatient()->loadRefDossierMedical();
  if ($type) {
    $doss_patient->loadRefsAntecedentsOfType($type);
  }
  else {
    $doss_patient->loadRefsAntecedents();
  }
  $tab_atc["CPatient"] = $doss_patient->_ref_antecedents_by_type;
  $tab_atc["CSejour"] = $dossier_medical->_ref_antecedents_by_type;

  foreach ($tab_atc["CSejour"] as $type => $ant_sej_type) {
    foreach ($ant_sej_type as $ant_id => $ant_sej) {
      if (isset($tab_atc["CPatient"][$type])) {
        foreach ($tab_atc["CPatient"][$type] as $ant_pat_id => $ant_pat) {
          if ($ant_pat->appareil == $ant_sej->appareil && $ant_pat->date == $ant_sej->date && $ant_pat->rques == $ant_sej->rques && $ant_pat->annule == $ant_sej->annule) {
            unset($tab_atc["CSejour"][$type][$ant_id]);
            unset($tab_atc["CPatient"][$type][$ant_pat_id]);
            $ant_communs[$type][] = $ant_pat;
          }
        }
      }
    }
    if (!count($tab_atc["CSejour"][$type]) || $type == "alle") {
      unset($tab_atc["CSejour"][$type]);
    }
  }
}
else {
  $tab_atc[$dossier_medical->object_class] = $dossier_medical->_ref_antecedents_by_type;
}

$smarty = new CSmartyDP();

$smarty->assign("tab_atc"     , $tab_atc);
$smarty->assign("ant_communs" , $ant_communs);
$smarty->assign("type"        , $type);
$smarty->assign("patient"     , $patient);

$smarty->display("inc_tooltip_atcd.tpl");