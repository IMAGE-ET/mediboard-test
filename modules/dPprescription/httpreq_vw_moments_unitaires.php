<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$can->needsRead();

$code_moment_id = mbGetValueFromGetOrSession("code_moment_id");

// Chargement du moment selectionné
$moment = new CBcbMoment();
$moment->load($code_moment_id);

// Chargement des associations
$moment->loadRefsAssociations();

// Chargememt de la liste des moments unitaires
$moment_unitaire = new CMomentUnitaire();
$moments_unitaires = $moment_unitaire->loadAllMoments();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("moment", $moment);
$smarty->assign("moments_unitaires", $moments_unitaires);
$smarty->assign("association", new CAssociationMoment());
$smarty->display("inc_vw_moments_unitaires.tpl");
?>