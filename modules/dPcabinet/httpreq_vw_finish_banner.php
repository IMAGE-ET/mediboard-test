<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );
require_once( $AppUI->getModuleClass('mediusers') );
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
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

$selConsult = mbGetValueFromGetOrSession("selConsult");
$_is_anesth = mbGetValueFromGet("_is_anesth");

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  $consult->loadRefsFwd();
  $userSel->load($consult->_ref_plageconsult->chir_id);
  $consult->loadAides($userSel->user_id);
  
  // On vérifie que l'utilisateur a les droits sur la consultation
  $right = false;
  foreach($listChir as $key => $value) {
    if($value->user_id == $consult->_ref_plageconsult->chir_id)
      $right = true;
  }
  if(!$right) {
    $AppUI->setMsg("Vous n'avez pas accès à cette consultation", UI_MSG_ALERT);
    $AppUI->redirect( "m=dPpatients&tab=0&id=$consult->patient_id");
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('_is_anesth', $_is_anesth);
$smarty->assign('consult', $consult);
$smarty->display('inc_finish_banner.tpl');

?>