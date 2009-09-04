<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

// Utilisateur sélectionné ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = mbGetValueFromGetOrSession("selConsult");
$_is_anesth = mbGetValueFromGet("_is_anesth");

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();
  $consult->_ref_patient->loadRefPhotoIdentite();
  $userSel->load($consult->_ref_plageconsult->chir_id);
  $consult->loadAides($userSel->user_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_is_anesth", $_is_anesth);
$smarty->assign("consult"   , $consult);

$smarty->display("inc_finish_banner.tpl");

?>