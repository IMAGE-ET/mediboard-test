<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();
$listHours = range(1, 24);

$hours_matin = range(0,12);
foreach($hours_matin as $_hour_matin){
	$listHoursMatin[] = str_pad($_hour_matin,2,"0",STR_PAD_LEFT);
}
$listHoursSoir = range(12, 24);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listHoursMatin", $listHoursMatin);
$smarty->assign("listHoursSoir", $listHoursSoir);
$smarty->assign("listHours", $listHours);

$smarty->display("configure.tpl");
?>