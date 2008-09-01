<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision$
* @author Alexis Granger
*/

global $can;
$can->needsAdmin();

$listHours = range(1, 24);

$hours_matin = range(0,12);
foreach($hours_matin as $_hour_matin){
	$listHoursMatin[] = str_pad($_hour_matin,2,"0",STR_PAD_LEFT);
}
$listHoursSoir = range(12, 24);

foreach($listHours as &$_hour){
	$_hour = str_pad($_hour,2,"0",STR_PAD_LEFT);
}

$heures_prise = CAppUI::conf("dPprescription CPrisePosologie heures_prise");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listHoursMatin", $listHoursMatin);
$smarty->assign("listHoursSoir", $listHoursSoir);
$smarty->assign("listHours", $listHours);
$smarty->assign("heures_prise", $heures_prise);

$smarty->display("configure.tpl");

?>