<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $canAdmin, $canRead, $canEdit, $m, $tab;

if(!$canAdmin) {
    $AppUI->redirect("m=system&a=access_denied");
}
if(!isset($dPconfig["dPImeds"]["url"])){
  $dPconfig["dPImeds"]["url"] = "http://10.100.0.67/listedossiers.aspx";
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("configurl" , $dPconfig["dPImeds"]["url"]);

$smarty->display("configure.tpl");
?>