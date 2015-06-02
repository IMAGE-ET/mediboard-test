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

CCanDo::checkEdit();

$patient_id      = CValue::getOrSession("patient_id");
$name            = CValue::get("name");
$firstName       = CValue::get("firstName");
$naissance_day   = CValue::get("naissance_day");
$naissance_month = CValue::get("naissance_month");
$naissance_year  = CValue::get("naissance_year");
$useVitale       = CValue::get("useVitale");
$covercard       = CValue::get("covercard");
$callback        = CValue::get("callback");
$modal           = CValue::get("modal", 0);

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefPhotoIdentite();
$patient->countDocItems();
$patient->loadRefsCorrespondantsPatient();
$patient->countINS();

// Chargement de l'ipp
$patient->loadIPP();
if (CModule::getActive("fse")) {
  $cv = CFseFactory::createCV();
  if ($cv) {
    $cv->loadIdVitale($patient);
  }
}

if (!$modal) {
  // Save history
  $params = array(
    "patient_id"      => $patient_id,
    "name"            => $name,
    "firstName"       => $firstName,
    "naissance_day"   => $naissance_day,
    "naissance_month" => $naissance_month,
    "naissance_year"  => $naissance_year,
  );
  CViewHistory::save($patient, $patient_id ? CViewHistory::TYPE_EDIT : CViewHistory::TYPE_NEW, $params);
}

if (!$patient_id) {
  $patient->nom           = $name;
  $patient->prenom        = $firstName;
  $patient->assure_nom    = $name;
  $patient->assure_prenom = $firstName;
  $patient->unescapeValues();

  if ($naissance_day && $naissance_month && $naissance_year) {
    $patient->naissance = sprintf('%04d-%02d-%02d', $naissance_year, $naissance_month, $naissance_day);
  }

  if (CAppUI::conf("dPpatients CPatient default_value_allow_sms", CGroups::loadCurrent())) {
    $patient->allow_sms_notification = 1;
  }
}

// Peut etre pas besoin de verifier si on n'utilise pas VitaleVision
if ($useVitale && CAppUI::pref('LogicielLectureVitale') == 'none' && CModule::getActive("fse")) {
  $patVitale = new CPatient();
  $cv = CFseFactory::createCV();
  if ($cv) {
    $cv->getPropertiesFromVitale($patVitale, CValue::sessionAbs('administrative_data'));
    $patVitale->nullifyEmptyFields();
    $patient->extendsWith($patVitale);
    $patient->updateFormFields();
    $patient->_bind_vitale = "1";
  }
}

if ($covercard && CModule::getActive("covercard")) {
  $covercardExec = CCoverCard::process($covercard);
  if ($covercardExec->queryNumber) {
    CCoverCard::updatePatientFromCC($patient, $covercardExec);
  }
}

// Chargement du nom_fr du pays de naissance
if ($patient_id) {
  $patient->updateNomPaysInsee();
}

$group = CGroups::loadCurrent();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("useVitale", $useVitale);
$smarty->assign('nom_jeune_fille_mandatory', CAppUI::conf("dPpatients CPatient nom_jeune_fille_mandatory", $group));
$smarty->assign("callback", $callback);
$smarty->assign("modal", $modal);

$smarty->display("vw_edit_patients.tpl");
