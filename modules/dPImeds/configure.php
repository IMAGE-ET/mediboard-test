<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();

if(!isset($dPconfig["dPImeds"]["url"])){
  $dPconfig["dPImeds"]["url"] = "http://10.100.0.67/listedossiers.aspx";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("configurl" , $dPconfig["dPImeds"]["url"]);

$smarty->display("configure.tpl");
?>