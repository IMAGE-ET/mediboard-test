<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m, $g, $dPconfig;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$view = mbGetValueFromGetOrSession("view", "day");
$userSel = new CMediusers;
$userSel->load($AppUI->user_id);

if(!$userSel->isPraticien()) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Consultations
$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date" , $date);
$smarty->assign("view" , $view);
$smarty->assign("vue"  , $vue);

$smarty->display("vw_mainboard.tpl");

?>