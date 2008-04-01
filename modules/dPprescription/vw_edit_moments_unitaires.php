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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("moments", $moments);

$smarty->display("vw_edit_moments_unitaires.tpl");

?>