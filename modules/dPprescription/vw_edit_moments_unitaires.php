<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;


// Chargement des moments unitaires
$moments = CMomentUnitaire::loadAllMoments();

// Liste d'heures et de minutes
$hours = array("08","12","14","18","22","24","02","06");
/*
$hours = range(0,23);
foreach($hours as &$hour){
	$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
}
*/


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("moments", $moments);
$smarty->assign("hours", $hours);
$smarty->display("vw_edit_moments_unitaires.tpl");

?>