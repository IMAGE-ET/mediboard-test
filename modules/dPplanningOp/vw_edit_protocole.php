<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab;

if(!$canEdit) {
	$AppUI->redirect("m=system&a=access_denied");
}

$protocole_id = mbGetValueFromGetOrSession("protocole_id", 0);
$chir_id      = mbGetValueFromGetOrSession("chir_id"     , 0);

// L'utilisateur est-il un praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien() and !$chir_id) {
  $chir_id = $mediuser->user_id;
}

// Chargement du praticien
$chir = new CMediusers;
if ($chir_id) {
  $chir->load($chir_id);
}

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

$protocole = new CProtocole;
if($protocole_id) {
  $protocole->load($protocole_id);
  $protocole->loadRefs();

  // On vérifie que l'utilisateur a les droits sur l'operation
  if (!array_key_exists($protocole->chir_id, $listPraticiens)) {
    $AppUI->setMsg("Vous n'avez pas accès à ce protocole", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&protocole_id=0"); 
  }
  $chir =& $protocole->_ref_chir;
}

// Heures & minutes
$start = 7;
$stop = 20;
$step = 15;

for ($i = $start; $i < $stop; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $step) {
    $mins[] = $i;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("protocole", $protocole);
$smarty->assign("chir"     , $chir);

$smarty->assign("listPraticiens", $listPraticiens);

$smarty->assign("hours", $hours);
$smarty->assign("mins" , $mins);

$smarty->display("vw_edit_protocole.tpl");

?>