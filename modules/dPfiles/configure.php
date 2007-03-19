<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();


$listNbFiles = mbArrayCreateRange(1,4,true);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listNbFiles"  , $listNbFiles);
$smarty->assign("configFiles" , $dPconfig["dPfiles"]);

$smarty->display("configure.tpl");

?>