<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$prescription_labo_id = mbGetValueFromGetOrSession("prescription_labo_id");

// Chargement de la prescription demandée
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_labo_id);
$prescription->loadRefs();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription", $prescription);

$smarty->display("inc_vw_examens_prescriptions.tpl");

?>
