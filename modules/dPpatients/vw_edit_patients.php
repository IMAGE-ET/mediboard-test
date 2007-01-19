<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Droit de lecture dPsante400
$moduleSante400 = CModule::getInstalled("dPsante400");
$canReadSante400 = $moduleSante400 ? $moduleSante400->canRead() : false;

$patient_id = mbGetValueFromGetOrSession("patient_id");
$dialog     = mbGetValueFromGet("dialog",0);
$name       = mbGetValueFromGet("name");
$firstName  = mbGetValueFromGet("firstName");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canReadSante400", $canReadSante400);
$smarty->assign("patient"        , $patient     );

$smarty->display("vw_edit_patients.tpl");
?>