<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

$consult_id = CValue::getOrSession("selConsult");
$user_id    = CValue::getOrSession("chirSel");
$_is_anesth = CValue::get("_is_anesth");

// Utilisateur sélectionné
$user = CMediusers::get($user_id);
$canUser = $user->canDo();
$canUser->needsEdit();

// Consultation courante
$consult = new CConsultation();
$consult->load($consult_id);
CCanDo::checkObject($consult);
$canConsult = $consult->canDo();
$canConsult->needsEdit();

$consult->loadRefPatient();
$consult->_ref_patient->loadRefPhotoIdentite();
$consult->loadRefPraticien()->loadRefFunction();
$consult->loadRefSejour();

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_is_anesth", $_is_anesth);
$smarty->assign("consult"   , $consult);

$smarty->display("inc_finish_banner.tpl");

?>