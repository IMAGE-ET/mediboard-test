<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$patient_id = CValue::getOrSession("patient_id");
$typeListe  = CValue::getOrSession("typeListe");

// Permettre de le remettre à null lors d'un changement de patient
CValue::getOrSession("prescription_labo_id");

// Chargement du patient

$patient = new CPatient;
$patient->load($patient_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("typeListe", $typeListe);

$smarty->display("vw_edit_prescriptions.tpl");

?>