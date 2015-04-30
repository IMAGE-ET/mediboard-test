<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$sejour_id    = CValue::get("sejour_id");
$date         = CValue::get("date");
$default_tab  = CValue::get("default_tab", CMedicament::getBase() == "vidal" ?
    "prescription_sejour" :
    "dossier_traitement" . (CAppUI::conf("soins Other vue_condensee_dossier_soins", CGroups::loadCurrent()) ? "_compact" : ""));
$popup        = CValue::get("popup", 0);
$modal        = CValue::get("modal", 0);
$operation_id = CValue::get("operation_id");
$mode_pharma  = CValue::get("mode_pharma", 0);

$sejour = new CSejour();
$sejour->load($sejour_id);
if (CModule::getActive("dPprescription")) {
  CPrescription::$_load_lite = true;
}

$sejour->loadRefGrossesse();

$sejour->loadRefPraticien();
$prescription_sejour = $sejour->loadRefPrescriptionSejour();
$sejour->loadJourOp($date);
$prescription_sejour->loadJourOp($date);
$prescription_sejour->loadRefCurrentPraticien();
$prescription_sejour->loadLinesElementImportant();

$patient = $sejour->loadRefPatient();
$patient->countINS();
$patient->loadRefsNotes();

$sejour->loadRefsOperations();
$sejour->loadRefCurrAffectation();

$dossier_medical_sejour = $sejour->loadRefDossierMedical();
if ($dossier_medical_sejour->_id) {
  $dossier_medical_sejour->loadRefsAllergies();
  $dossier_medical_sejour->loadRefsAntecedents();
  $dossier_medical_sejour->countAntecedents(false);
  $dossier_medical_sejour->countAllergies();
}
$patient->loadRefPhotoIdentite();
$dossier_medical_patient = $patient->loadRefDossierMedical();
$patient->loadRefLatestConstantes(null, array("poids", "taille"));
if ($dossier_medical_patient->_id) {
  $dossier_medical_patient->loadRefsAllergies();
  $dossier_medical_patient->loadRefsAntecedents();
  $dossier_medical_patient->countAntecedents(false);
  $dossier_medical_patient->countAllergies();
}

/* Suppression des antecedents du dossier medical du patients présent dans le dossier medical du sejour */
if ($dossier_medical_patient->_id && $dossier_medical_sejour->_id) {
  CDossierMedical::cleanAntecedentsSignificatifs($dossier_medical_sejour, $dossier_medical_patient);
}

$operation = new COperation();
if ($operation->load($operation_id)) {
  $operation->loadRefPlageOp();
  $operation->_ref_anesth->loadRefFunction();
}
$is_praticien = CAppUI::$user->isPraticien();

if (CModule::getActive("dPprescription")) {
  CPrescription::$_load_lite = false;
}

$smarty = new CSmartyDP();

$smarty->assign("sejour"          , $sejour);
$smarty->assign("patient"         , $patient);
$smarty->assign("date"            , $date);
$smarty->assign("default_tab"     , $default_tab);
$smarty->assign("popup"           , $popup);
$smarty->assign("modal"           , $modal);
$smarty->assign("operation_id"    , $operation_id);
$smarty->assign("mode_pharma"     , $mode_pharma);
$smarty->assign("is_praticien"    , $is_praticien);
$smarty->assign("mode_protocole"  , CValue::getOrSession("mode_protocole", 0));
$smarty->assign("operation"       , $operation);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("isPrescriptionInstalled" , CModule::getActive("dPprescription"));

$smarty->display("inc_dossier_sejour.tpl");
