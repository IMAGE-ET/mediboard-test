<?php /* $Id: vw_edit_planning.php 66 2006-05-14 21:06:12Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 66 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab, $dPconfig;

require_once( $AppUI->getModuleClass("dPplanningOp", "sejour"));
require_once( $AppUI->getModuleClass('mediusers'));
require_once( $AppUI->getModuleClass('dPpatients', 'patients'));

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$patient_id = mbGetValueFromGet("patient_id");
$praticien_id = mbGetValueFromGetOrSession("praticien_id");

// L'utilisateur est-il un praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien() and !$praticien_id) {
  $praticien_id = $mediuser->user_id;
}

// Chargement du praticien
$praticien = new CMediusers;
if ($praticien_id) {
  $praticien->load($praticien_id);
}

// Chargement du patient
$patient = new CPatient;
if ($patient_id) {
  $patient->load($patient_id);
}

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

// On récupère le séjour
$sejour = new CSejour;
if ($sejour_id) {
  $sejour->load($sejour_id);

  // On vérifie que l'utilisateur a les droits sur l'operation
  if (!array_key_exists($sejour->praticien_id, $listPraticiens)) {
    $AppUI->setMsg("Vous n'avez pas accès à ce séjour", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&sejour_id=0");
  }

  $sejour->loadRefs();
  
  foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
    $operation =& $sejour->_ref_operations[$keyOp];
    $operation->loadRefsFwd();
  }

  $praticien =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
}

// Heures & minutes
$sejourConfig =& $dPconfig["dPplanningOp"]["sejour"];
for ($i = $sejourConfig["heure_deb"]; $i <= $sejourConfig["heure_fin"]; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $sejourConfig["min_intervalle"]) {
    $mins[] = $i;
}

// Création du template
require_once($AppUI->getSystemClass ("smartydp" ));
$smarty = new CSmartyDP;

$smarty->assign("sejour", $sejour);
$smarty->assign("praticien" , $praticien);
$smarty->assign("patient"  , $patient);

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("hours", $hours);
$smarty->assign("mins", $mins);

$smarty->display("vw_edit_sejour.tpl");

?>