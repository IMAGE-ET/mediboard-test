<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers;
$user->load($AppUI->user_id);

$patient_id = mbGetValueFromGetOrSession("patient_id");

$prescription_labo_id = mbGetValueFromGetOrSession("prescription_labo_id");

if (!$patient_id) {
  return;
}

// Chargement de la prescription demandée
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_labo_id);
$prescription->loadRefs();

$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefsPrescriptions(PERM_EDIT);
foreach ($patient->_ref_prescriptions as $_prescription) {
  $_prescription->loadRefs();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"     , $patient     );
$smarty->assign("prescription", $prescription);

$smarty->display("inc_vw_prescriptions.tpl");

?>
