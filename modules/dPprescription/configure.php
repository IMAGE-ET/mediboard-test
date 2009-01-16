<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision$
* @author Alexis Granger
*/

global $can;
$can->needsAdmin();

$listHours = range(0, 23);
foreach($listHours as &$_hour){
	$_hour = str_pad($_hour,2,"0",STR_PAD_LEFT);
}

// Chargement des etablissements
$group = new CGroups();
$groups = $group->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("listHours", $listHours);
$smarty->assign("groups", $groups);
$smarty->display("configure.tpl");

?>