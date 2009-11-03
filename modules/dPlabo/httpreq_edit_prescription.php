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
$prescription->load($prescription_labo_id = CValue::get("prescription_labo_id"));
if (!$prescription->_id) {
  $prescription->patient_id = CValue::get("patient_id");
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
