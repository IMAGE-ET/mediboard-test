<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$prescription_id = mbGetValueFromGet("prescription_id");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$prescription->loadRefObject();
$prescription->_ref_object->loadRefPatient();
$prescription->_ref_object->_ref_patient->loadRefDossierMedical();
$prescription->_ref_object->_ref_patient->_ref_dossier_medical->loadRefsAntecedents();

$antecedents = $prescription->_ref_object->_ref_patient->_ref_dossier_medical->_ref_antecedents;
$allergies = $antecedents["alle"];

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("allergies", $allergies);
$smarty->display("inc_vw_allergies.tpl");


?>