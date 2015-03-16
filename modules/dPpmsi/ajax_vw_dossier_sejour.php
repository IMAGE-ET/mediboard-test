<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkEdit();

$group = CGroups::loadCurrent();

// Chargement du patient
$patient = new CPatient();
$patient->load(CValue::get("patient_id"));
$patient->loadIPP();
$patient->loadRefsCorrespondants();
$patient->loadRefPhotoIdentite();
$patient->loadPatientLinks();
$patient->countINS();
if (CModule::getActive("fse")) {
  $cv = CFseFactory::createCV();
  if ($cv) {
    $cv->loadIdVitale($patient);
  }
}

// Chargement du séjour
$sejour  = new CSejour();
$sejour->load(CValue::get("sejour_id"));
if ($sejour->patient_id == $patient->_id) {
  $sejour->_ref_patient = $patient;
  $sejour->canDo();
  $sejour->loadNDA();
  $sejour->loadExtDiagnostics();
  $sejour->loadRefsAffectations();
  $sejour->loadRefsOperations();
  $sejour->loadSuiviMedical();
  foreach ($sejour->_ref_operations as $_op) {
    $_op->loadRefPraticien();
    $_op->loadRefPlageOp();
    $_op->loadRefAnesth();
    $_op->loadRefsConsultAnesth();
    $_op->loadBrancardage();
  }
  $sejour->loadRefsConsultAnesth();
}
else {
  $sejour = new CSejour();
}

// Création du template
$smarty = new CSmartyDP("modules/dPpmsi");

$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("hprim21installed", CModule::getActive("hprim21"));
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC($group)));
$smarty->assign("patient"         , $patient);
$smarty->assign("sejour"          , $sejour);


$smarty->display("inc_vw_dossier_sejour.tpl");