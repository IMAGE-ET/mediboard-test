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

$user = CMediusers::get();

$patient_id   = CValue::getOrSession("patient_id", 0);
$vw_cancelled = CValue::get("vw_cancelled", 0);

// recuperation des id dans le cas d'une recherche de dossiers cliniques 
$consultation_id = CValue::get("consultation_id", 0);
$sejour_id       = CValue::get("sejour_id", 0);
$operation_id    = CValue::get("operation_id", 0);

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// Récuperation du patient sélectionné
$patient = new CPatient;
$patient->load($patient_id);
if (!$patient->_id || $patient->_vip) {
  CAppUI::setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  CAppUI::redirect("m=patients&tab=vw_idx_patients");
}
$patient->loadDossierComplet(PERM_READ);

$patient->_nb_files_docs -= $patient->_nb_cancelled_files;

// Chargement de l'IPP
$patient->loadIPP();
$patient->countINS();

// Chargement du dossier medical du patient
$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadComplete();

// Suppression des consultations d'urgences
foreach ($patient->_ref_consultations as $consult) {
  if ($consult->motif == "Passage aux urgences") {
    unset($patient->_ref_consultations[$consult->_id]);
  }
}

$nb_sejours_annules = 0;
$nb_ops_annulees = 0;

// Masquer par défault les interventions et séjours annulés
if (!$vw_cancelled) {
  foreach ($patient->_ref_sejours as $_key => $_sejour) {
    foreach ($_sejour->_ref_operations as $_key_op => $_operation) {
      if ($_operation->annulee) {
        unset ($_sejour->_ref_operations[$_key_op]);
        $nb_ops_annulees++;
      }
    }
    if ($_sejour->annule) {
      unset($patient->_ref_sejours[$_key]);
      $nb_sejours_annules++;
    }
  }
}

$patient->_ref_dossier_medical->canDo();

$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $user->isFromType(array("Infirmière"));
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canCabinet", CModule::getCanDo("dPcabinet"));

$smarty->assign("consultation_id"         , $consultation_id);
$smarty->assign("sejour_id"               , $sejour_id);
$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("operation_id"            , $operation_id);
$smarty->assign("patient"                 , $patient);
$smarty->assign("listPrat"                , $listPrat);
$smarty->assign("object"                  , $patient);
$smarty->assign("isImedsInstalled"        , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("nb_sejours_annules"      , $nb_sejours_annules);
$smarty->assign("nb_ops_annulees"          , $nb_ops_annulees);
$smarty->assign("vw_cancelled"            , $vw_cancelled);

$smarty->display("vw_full_patients.tpl");
