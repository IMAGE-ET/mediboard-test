<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Thomas Despoix
 */
 
global $can;
$can->needsRead();

// Chargement de la prescription
$prescription = new CPrescriptionLabo;
if ($prescription->load(mbGetValueFromGetOrSession("prescription_id"))) {
  $prescription->loadRefsBack();
  $prescription->loadClassification();
}

// Chargement du patient
$patient_id = mbGetValue($prescription->patient_id, mbGetValueFromGetOrSession("patient_id"));
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsPrescriptions(PERM_EDIT);

// Chargement de la première prescription dans le cas ou il n'y en a pas
if(!$prescription->_id && $patient->_id && count($patient->_ref_prescriptions)) {
  $prescription->load(reset($patient->_ref_prescriptions)->_id);
  $prescription->loadRefsBack();
  $prescription->loadClassification();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("prescription"  , $prescription);

$smarty->display("vw_resultats.tpl");

?>