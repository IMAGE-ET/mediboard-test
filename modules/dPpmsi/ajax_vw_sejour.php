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
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement du séjour précis du patient
$sejour = new CSejour();
$sejour->load($sejour_id);
if ($sejour->group_id != CGroups::loadCurrent()->_id) {
  CAppUI::redirect("m=system&a=access_denied");
}
$sejour->loadRefPatient();

$patient = $sejour->_ref_patient;
$patient->loadRefsFwd();
$patient->loadRefPhotoIdentite();
$patient->loadRefsCorrespondants();
$patient->loadRefDossierMedical();
$patient->_ref_dossier_medical->updateFormFields();
$patient->loadIPP();

// Chargement des séjours du Patient
$sejours = $patient->loadRefsSejours();
$isSejourPatient = null;

if (array_key_exists($sejour_id, $patient->_ref_sejours)) {
  $isSejourPatient = $sejour_id;
}
foreach ($sejours as $_sej) {
  $_sej->loadRefPraticien();
  $_sej->loadRefsOperations();
  $_sej->loadNDA();
  $_sej->canDo();
  foreach ($_sej->_ref_operations as $_op) {
    $_op->countDocItems();
    $_op->canDo();
  }
}

// Dossier médical
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
$sejour->countExchanges();
$sejour->loadNDA();
$sejour->loadRefsOperations();
$sejour->loadRefsConsultations();
$sejour->loadRefsActes();
$sejour->canDo();

foreach ($sejour->_ref_consultations as $consult) {
  $consult->loadRefPlageConsult();
  $consult->loadExtCodesCCAM();
  $consult->loadRefsActes();
  $consult->loadRefConsultAnesth();
  $consult->loadRefPatient()->loadRefConstantesMedicales();
  foreach ($consult->_ref_actes as $_acte) {
    $_acte->loadRefExecutant();
  }
}

foreach ($sejour->_ref_actes as $_acte) {
  $_acte->loadRefExecutant();
}
foreach ($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->countExchanges();
  $_operation->countDocItems();
  $_operation->loadRefsActes();
  $_operation->canDo();
  if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
    $_operation->_ref_sejour->loadRefsFwd();
    foreach ($_operation->_ext_codes_ccam as $key => $value) {
      $_operation->_ext_codes_ccam[$key] = CDatedCodeCCAM::get($value->code);
    }
    $_operation->getAssociationCodesActes();
    $_operation->loadPossibleActes();
    $_operation->_ref_plageop->loadRefsFwd();
    $_operation->loadRefPraticien();

    // Chargement des règles de codage
    $_operation->loadRefsCodagesCCAM();
    foreach ($_operation->_ref_codages_ccam as $_codage) {
      $_codage->loadPraticien()->loadRefFunction();
      $_codage->loadActesCCAM();
      foreach ($_codage->_ref_actes_ccam as $_acte) {
        $_acte->getTarif();
      }
    }
  }
  else {
    foreach ($_operation->_ref_actes_ccam as $_acte) {
      $_acte->loadRefExecutant();
      $_acte->loadRefCodeCCAM();
      $_acte->guessAssociation();
    }
  }
  if ($_operation->plageop_id) {
    $_operation->_ref_plageop->loadRefsFwd();
  }
  
  $consult_anest = $_operation->_ref_consult_anesth;
  if ($consult_anest->consultation_anesth_id) {
    $consult_anest->loadRefsFwd();
    $consult_anest->_ref_plageconsult->loadRefsFwd();
  }
}

//// Création/Chargement du rss
//$rss = new CRSS();
//$whereIs = "`sejour_id` = ".$sejour_id;
//$rss->loadList($whereIs);
//
//if (!$rss->rss_id) {
//  $rss->sejour_id = $sejour_id;
//  $rss->loadMatchingObject();
//  if ($msg = $rss->store()) {
//    CAppUI::stepAjax($msg, UI_MSG_WARNING);
//  }
//}
//
////Création des RUM du RSS
//$affectations = $sejour->loadRefsAffectations();
//$list_rum = null;
//if ($affectations) {
//  foreach ($affectations as $key => $affectation) {
//    $rum = new CRUM();
//    $where["rss_id"] = " = ".$rss->rss_id;
//    $where["affectation_id"] = " = ".$affectation->affectation_id;
//    $rum->loadList($where);
//    if (!$rum->rum_id) {
//      $rum->rss_id = $rss->rss_id;
//      $rum->affectation_id = $affectation->affectation_id;
//      $rum->loadMatchingObject();
//      $rum->loadMedicalesInfos();
//      if ($msg = $rum->store()) {
//        CAppUI::stepAjax($msg, UI_MSG_WARNING);
//      }
//    }
//
//    $list_rum[$key] = $rum;
//  }
//}
//else {
//  $rum = new CRUM();
//  $rum->rss_id = $rss->rss_id;
//  $list_rum[] = $rum;
//}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("hprim21installed", CModule::getActive("hprim21"));
$smarty->assign("sejour"  , $sejour );
$smarty->assign("listPrat", $listPrat);
//$smarty->assign("rss", $rss);
//$smarty->assign("list_rum", $list_rum);
$smarty->assign("patient", $patient);
$smarty->assign("isSejourPatient" , $isSejourPatient);

$smarty->display("inc_vw_sejour.tpl");
