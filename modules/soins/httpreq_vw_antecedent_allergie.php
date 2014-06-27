<?php
/**
 * $Id:$
 *
 * @category soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision:$
 */

$sejour_id = CValue::getOrSession("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPatient();
$sejour->loadRefDossierMedical();
$dossier_medical_sejour =& $sejour->_ref_dossier_medical;
if ($dossier_medical_sejour->_id) {
  $dossier_medical_sejour->loadRefsAllergies();
  $dossier_medical_sejour->loadRefsAntecedents();
  $dossier_medical_sejour->countAntecedents(false);
  $dossier_medical_sejour->countAllergies();
}

$patient =& $sejour->_ref_patient;
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;
if ($dossier_medical->_id) {
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents(false);
  $dossier_medical->countAllergies();
}

if ($dossier_medical_sejour->_id && $dossier_medical->_id) {
  CDossierMedical::cleanAntecedentsSignificatifs($dossier_medical_sejour, $dossier_medical);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour_id"             , $sejour->_id);
$smarty->assign("antecedents_sejour"    , $dossier_medical_sejour->_ref_antecedents_by_type);
$smarty->assign("dossier_medical_sejour", $dossier_medical_sejour);
$smarty->assign("antecedents"           , $dossier_medical->_ref_antecedents_by_type);
$smarty->assign("dossier_medical"       , $dossier_medical);
$smarty->assign("patient_guid"          , $patient->_guid);
$smarty->display("inc_antecedents_allergies.tpl");