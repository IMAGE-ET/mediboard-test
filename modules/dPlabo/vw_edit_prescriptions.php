<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$typeListe  = mbGetValueFromGetOrSession("typeListe");

// Permettre de le remettre à null lors d'un changement de patient
mbGetValueFromGetOrSession("prescription_labo_id");

// Chargement du patient

$patient = new CPatient;
$patient->load($patient_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("typeListe", $typeListe);

$smarty->display("vw_edit_prescriptions.tpl");

?>