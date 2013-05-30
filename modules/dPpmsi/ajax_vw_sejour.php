<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$sejour_id = CValue::getOrSession("sejour_id");

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefPatient();

$patient = $sejour->_ref_patient;
$patient->loadRefsFwd();
$patient->loadRefPhotoIdentite();
$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->updateFormFields();
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();
$patient->loadIPP();

$sejour->loadRefPrescriptionSejour();
$dossier_medical = $sejour->loadRefDossierMedical();
$dossier_medical->updateFormFields();
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();
$sejour->loadRefsAffectations();
$sejour->loadExtDiagnostics();
$sejour->loadRefs();
$sejour->countExchanges();
$sejour->loadRefGHM();
$sejour->loadNDA();
$sejour->canRead();
$sejour->canEdit();
foreach ($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->countExchanges();
  $_operation->countDocItems();
  $_operation->loadRefsActesCCAM();
  $_operation->canRead();
  $_operation->canEdit();
  foreach ($_operation->_ref_actes_ccam as $_acte) {
    $_acte->loadRefsFwd();
    $_acte->guessAssociation();
  }
  if ($_operation->plageop_id) {
    $plage = $_operation->_ref_plageop;
    $plage->loadRefsFwd();
  }
  
  $consult_anest = $_operation->_ref_consult_anesth;
  if ($consult_anest->consultation_anesth_id) {
    $consult_anest->loadRefsFwd();
    $consult_anest->_ref_plageconsult->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("hprim21installed", CModule::getActive("hprim21"));
$smarty->assign("sejour" , $sejour );
$smarty->assign("listPrat", $listPrat);

$smarty->display("inc_vw_sejour.tpl");
