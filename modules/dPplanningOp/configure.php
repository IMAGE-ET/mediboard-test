<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();

$listHours = range(0, 23);
$listInterval = array("5","10","15","20","30");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listHours"    , $listHours);
$smarty->assign("listInterval" , $listInterval);

$smarty->display("configure.tpl");
?>