<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */	      

global $AppUI, $can, $m;

$can->needsRead();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->loadMatchingObject();

// Chargement des medicaments
if ($prescription->_id) {
  $prescription->loadRefsLinesMed();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->display("inc_vw_prescription_meds.tpl");

?>