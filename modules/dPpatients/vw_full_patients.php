<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$patient_id = CValue::getOrSession("patient_id", 0);

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
if(!$patient->_id) {
  $AppUI->setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=0");
}
$patient->loadDossierComplet(PERM_READ);

//Chargement de l'IPP
$patient->loadIPP();

// Chargement du dossier medical du patient
$patient->loadRefDossierMedical();
$patient->_ref_dossier_medical->loadRefsTraitements();
$patient->_ref_dossier_medical->loadRefsAntecedents();

// Suppression des consultations d'urgences
foreach($patient->_ref_consultations as $keyConsult => $consult){
  if($consult->motif == "Passage aux urgences"){
    unset($patient->_ref_consultations[$keyConsult]);
  }
}

$patient->_ref_dossier_medical->canRead();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canCabinet", CModule::getCanDo("dPcabinet"));

$smarty->assign("consultation_id"   , $consultation_id    );
$smarty->assign("sejour_id"         , $sejour_id          );
$smarty->assign("operation_id"      , $operation_id       );
$smarty->assign("patient"           , $patient            );
$smarty->assign("listPrat"          , $listPrat           );
$smarty->assign("object"            , $patient            );
$smarty->assign("isImedsInstalled"  , CModule::getActive("dPImeds"));

$smarty->display("vw_full_patients.tpl");

?>