<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;

// Recuperation des valeurs
$code_moment_id = mbGetValueFromGetOrSession("code_moment_id");

// Chargement du moment selectionné
$moment = new CBcbMoment();
$moment->load($code_moment_id);

// Chargement des moments unitaires
$moments_unitaires = CMomentUnitaire::loadAllMoments();

// Chargement des associations du moment
$moment->loadRefsAssociations();

// Chargement des moments
$moments = CBcbMoment::loadAllMoments();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("association", new CAssociationMoment());
$smarty->assign("moments", $moments);
$smarty->assign("moments_unitaires", $moments_unitaires);
$smarty->assign("moment", $moment);

$smarty->display("vw_edit_associations_moments.tpl");

?>