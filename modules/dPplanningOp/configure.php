<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $canAdmin, $canRead, $canEdit, $m, $tab;

$listHours = range(0, 23);
$listInterval = array("5","10","15","20","30");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listHours"    , $listHours);
$smarty->assign("listInterval" , $listInterval);
$smarty->assign("configOper"   , $dPconfig["dPcabinets"]["plages"]);
$smarty->assign("configSejour" , $dPconfig["dPplanningOp"]["sejour"]);

$smarty->display("configure.tpl");
?>