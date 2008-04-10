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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listHours", $listHours);

$smarty->display("configure.tpl");
?>