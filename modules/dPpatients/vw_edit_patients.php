<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $can, $AppUI;

$can->needsEdit();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$name       = mbGetValueFromGet("name");
$firstName  = mbGetValueFromGet("firstName");
$naissance_day   = mbGetValueFromGet("naissance_day");
$naissance_month = mbGetValueFromGet("naissance_month");
$naissance_year  = mbGetValueFromGet("naissance_year");
$useVitale  = mbGetValueFromGet("useVitale");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();
$patient->loadRefPhotoIdentite();

// Chargement de l'ipp
$patient->loadIPP();
$patient->loadIdVitale();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
	$patient->assure_nom    = $name;
  $patient->assure_prenom = $firstName;
	
	if ($naissance_day && $naissance_month && $naissance_year) {
		$patient->naissance = sprintf('%04d-%02d-%02d', $naissance_year, $naissance_month, $naissance_day);
	}
}

// Peut etre pas besoin de verifier si on n'utilise pas VitaleVision
if ($useVitale && $AppUI->user_prefs['GestionFSE'] && !$AppUI->user_prefs['VitaleVision']) {
  $patVitale = new CPatient();
  $patVitale->getValuesFromVitale();
  $patVitale->nullifyEmptyFields();
  $patient->extendsWith($patVitale);
  $patient->updateFormFields();
  $patient->_bind_vitale = "1";
}

// Chargement du nom_fr du pays de naissance
if($patient_id)
  $patient->updateNomPaysInsee();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->assign("useVitale", $useVitale);
$smarty->display("vw_edit_patients.tpl");
