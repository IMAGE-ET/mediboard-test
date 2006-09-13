<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
  
if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Utilisateur sélectionné ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$selConsult = mbGetValueFromGetOrSession("selConsult", 0);
if (isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", 0);
}

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
$consult->_ref_consult_anesth->consultation_anesth_id = 0;
if ($selConsult) {
  $consult->load($selConsult);
  $consult->loadRefs();
  $userSel->load($consult->_ref_plageconsult->chir_id);
  $userSel->loadRefs();
  $consult->loadAides($userSel->user_id);
  
  // On vérifie que l'utilisateur a les droits sur la consultation
  $right = false;
  foreach($listChir as $key => $value) {
    if($value->user_id == $consult->_ref_plageconsult->chir_id)
      $right = true;
  }
  if(!$right) {
    $AppUI->setMsg("Vous n'avez pas accès à cette consultation", UI_MSG_ALERT);
    $AppUI->redirect("m=dPpatients&tab=0&id=$consult->patient_id");
  }
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadStaticCIM10($userSel->user_id);
  foreach ($patient->_ref_consultations as $key => $value) {
    $patient->_ref_consultations[$key]->loadRefs();
    $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefs();
  }
  foreach ($patient->_ref_sejours as $key => $sejour) {
    $patient->_ref_sejours[$key]->loadRefsFwd();
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }

  $consult_anesth =& $consult->_ref_consult_anesth;
  
}
// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("consult"       , $consult);
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("patient"       , $patient);

$smarty->display("inc_consult_anesth/interventions.tpl");

?>