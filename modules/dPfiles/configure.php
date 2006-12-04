<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $canAdmin, $canRead, $canEdit, $m, $tab;

if(!$canAdmin) {
    $AppUI->redirect("m=system&a=access_denied");
}




$listNbFiles = mbArrayCreateRange(1,4,true);


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("listNbFiles"  , $listNbFiles);
$smarty->assign("configFiles" , $dPconfig["dPfiles"]);

$smarty->display("configure.tpl");

?>