<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

// Utilisateur sélectionné ou utilisateur courant
$prat_id = CValue::getOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  CAppUI::setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = CValue::getOrSession("selConsult", 0);

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;

if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();
  $consult->loadRefConsultAnesth();

  if($consult->_ref_consult_anesth->_id) {
    $consult->_ref_consult_anesth->loadRefs();
    $consult->_ref_consult_anesth->_ref_operation->loadRefSejour();
    $consult->_ref_consult_anesth->_ref_operation->_ref_sejour->loadRefDossierMedical();
    $consult->_ref_consult_anesth->_ref_sejour->loadRefPraticien();
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefsSejours();
  foreach ($patient->_ref_sejours as $key => $sejour) {
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
} else {
  $consult->_ref_consult_anesth = new CConsultAnesth();
}

$consult_anesth =& $consult->_ref_consult_anesth;
$nextSejourAndOperation = $patient->getNextSejourAndOperation($consult->_ref_plageconsult->date);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult"               , $consult);
$smarty->assign("consult_anesth"        , $consult_anesth);
$smarty->assign("patient"               , $patient);
$smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
$smarty->assign("listChirs"      , $listChirs);

$smarty->display("inc_consult_anesth/interventions.tpl");

?>