<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$typeListe  = mbGetValueFromGetOrSession("typeListe", "pack");

// Chargement du patient

$patient = new CPatient;
$patient->load($patient_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $patient);
$smarty->assign("typeListe", $typeListe);

$smarty->display("vw_edit_prescriptions.tpl");

?>