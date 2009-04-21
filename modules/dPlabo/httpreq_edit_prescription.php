<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can;

$can->needsRead();

$user = new CMediusers();

$listPrats = $user->loadPraticiens(PERM_EDIT);

// Chargement de la prescription choisie
$prescription = new CPrescriptionLabo;
$prescription->load($prescription_labo_id = mbGetValueFromGet("prescription_labo_id"));
if (!$prescription->_id) {
  $prescription->patient_id = mbGetValueFromGet("patient_id");
  $prescription->date = mbDateTime();
  $prescription->praticien_id = $AppUI->user_id;
}

$prescription->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription", $prescription);
$smarty->assign("listPrats"   , $listPrats);

$smarty->display("inc_edit_prescription.tpl");

?>
