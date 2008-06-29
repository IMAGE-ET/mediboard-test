<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: $
* @author Romain OLLIVIER
*/

include (CAppUI::getModuleFile("dPboard", "inc_board"));

$date = mbGetValueFromGetOrSession("date", mbDate());
$prec = mbDate("-1 week", $date);
$suiv = mbDate("+1 week", $date);

// Consultations
$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);

// Variables de templates
$smarty->assign("date"           , $date);
$smarty->assign("prec"           , $prec);
$smarty->assign("suiv"           , $suiv);

$smarty->assign("vue"            , $vue);

$smarty->display("vw_week.tpl");

?>