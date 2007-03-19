<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());
$view = mbGetValueFromGetOrSession("view", "week");
$userSel = new CMediusers;
$userSel->load($AppUI->user_id);

if(!$userSel->isPraticien()) {
  $AppUI->redirect("m=system&a=access_denied");
}

if($view == "day"){
  $prec = mbDate("-1 day", $date);
  $suiv = mbDate("+1 day", $date);
}else{
  $prec = mbDate("-1 week", $date);
  $suiv = mbDate("+1 week", $date);
}

// Consultations
$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prec" , $prec);
$smarty->assign("suiv" , $suiv);
$smarty->assign("date" , $date);
$smarty->assign("view" , $view);
$smarty->assign("vue"  , $vue);

$smarty->display("vw_mainboard.tpl");

?>