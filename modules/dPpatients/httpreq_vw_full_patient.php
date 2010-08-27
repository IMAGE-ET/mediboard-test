<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$patient_id = CValue::getOrSession("patient_id", 0);

if(!$patient_id) {
  CAppUI::setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  CAppUI::redirect("m=dPpatients&tab=0");
}

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// Récuperation du patient sélectionné
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet(PERM_READ);
$patient->loadRefDossierMedical();
$patient->_ref_dossier_medical->loadRefsAntecedents();
$patient->_ref_dossier_medical->loadRefsTraitements();

$userSel = new CMediusers;
$userSel->load($AppUI->user_id);

// Suppression des consultations d'urgences
foreach($patient->_ref_consultations as $keyConsult => $consult){
  if($consult->motif == "Passage aux urgences"){
    unset($patient->_ref_consultations[$keyConsult]);
  }
}

$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $AppUI->_ref_user->isFromType(array("Infirmière"));

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canCabinet", CModule::getCanDo("dPcabinet"));

$smarty->assign("listPrat"                , $listPrat);
$smarty->assign("patient"                 , $patient);
$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("isImedsInstalled"        , CModule::getActive("dPImeds"));

$smarty->display("inc_vw_full_patients.tpl");
?>