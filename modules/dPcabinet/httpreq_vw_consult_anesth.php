<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

// Utilisateur sélectionné ou utilisateur courant
$prat_id = CValue::getOrSession("chirSel", 0);
$dossier_anesth_id = CValue::get("dossier_anesth_id");

$userSel = CMediusers::get($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = CValue::getOrSession("selConsult", 0);

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;

if ($selConsult) {
  $consult->load($selConsult);
  
  // Vérification de droits
  CCanDo::checkObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();

  // Chargement de la consult anesthésie
  $consult->loadRefConsultAnesth();
  $consultAnesth = $consult->_ref_consult_anesth;
  
  if ($dossier_anesth_id) {
    $consultAnesth = $consult->_refs_dossiers_anesth[$dossier_anesth_id];
  }
  
  if ($consultAnesth->_id) {
    $consultAnesth->loadRefOperation();
    $consultAnesth->loadRefConsultation();
    $consultAnesth->_ref_consultation->loadRefPraticien();
    $consultAnesth->_ref_operation->loadRefChir(true);
    $consultAnesth->_ref_sejour->loadRefDossierMedical();
    $consultAnesth->_ref_sejour->loadRefPraticien(true);
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefsSejours();
  foreach ($patient->_ref_sejours as $_sejour) {
    $_sejour->loadRefsOperations();
    foreach ($_sejour->_ref_operations as $_operation) {
      $_operation->loadRefPlageOp(true);
      $_operation->loadRefChir(true);
    }
  }
} 
else {
  $consult->_ref_consult_anesth = new CConsultAnesth();
}

$consult_anesth =& $consult->_ref_consult_anesth;
$nextSejourAndOperation = $patient->getNextSejourAndOperation($consult->_ref_plageconsult->date);

$listChirs = CAppUI::pref("pratOnlyForConsult", 1) ?
  $userSel->loadPraticiens() :
  $userSel->loadProfessionnelDeSante();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"               , $consult);
$smarty->assign("consult_anesth"        , $consult_anesth);
$smarty->assign("patient"               , $patient);
$smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
$smarty->assign("listChirs"             , $listChirs);

$smarty->display("inc_consult_anesth/interventions.tpl");

?>