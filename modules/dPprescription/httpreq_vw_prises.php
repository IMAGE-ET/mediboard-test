<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_line_id = mbGetValueFromGetOrSession("prescription_line_id");

// Chargement de la ligne de prescription
$prescription_line = new CPrescriptionLineMedicament();
$prescription_line->load($prescription_line_id);

// Chargement des prises de la ligne de prescription
$prescription_line->loadRefsPrises();

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMoments();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("curr_line"       , $prescription_line);
$smarty->assign("moments"         , $moments);

$smarty->display("inc_vw_prises.tpl");

?>