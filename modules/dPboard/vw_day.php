<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision$
* @author Romain OLLIVIER
*/

CAppUI::requireModuleFile("dPboard", "inc_board");

$date = mbGetValueFromGetOrSession("date", mbDate());
$prec = mbDate("-1 day", $date);
$suiv = mbDate("+1 day", $date);

// Consultations
$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);

global $smarty;

// Variables de templates
$smarty->assign("date", $date);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("vue",  $vue);

$smarty->display("vw_day.tpl");

?>