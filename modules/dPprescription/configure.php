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

$listHoursMatin = range(0,12);
$listHoursSoir = range(13, 24);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listHoursMatin", $listHoursMatin);
$smarty->assign("listHoursSoir", $listHoursSoir);
$smarty->assign("listHours", $listHours);

$smarty->display("configure.tpl");
?>