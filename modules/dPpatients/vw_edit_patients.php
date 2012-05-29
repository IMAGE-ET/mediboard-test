<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $can;

$can->needsEdit();

$patient_id = CValue::getOrSession("patient_id");
$name       = CValue::get("name");
$firstName  = CValue::get("firstName");
$naissance_day   = CValue::get("naissance_day");
$naissance_month = CValue::get("naissance_month");
$naissance_year  = CValue::get("naissance_year");
$useVitale  = CValue::get("useVitale");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefPhotoIdentite();
$patient->countDocItems();

// Chargement de l'ipp
$patient->loadIPP();
if (CModule::getActive("fse")) {
  $cv = CFseFactory::createCV();
  if ($cv) {
    $cv->loadIdVitale($patient);
  }
}

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
if ($useVitale && !CAppUI::pref('VitaleVision') && CModule::getActive("fse")) {
  $patVitale = new CPatient();
  $cv = CFseFactory::createCV();
  if ($cv) {
    $cv->getPropertiesFromVitale($patVitale);
    $patVitale->nullifyEmptyFields();
    $patient->extendsWith($patVitale);
    $patient->updateFormFields();
    $patient->_bind_vitale = "1";
  }
}

// Chargement du nom_fr du pays de naissance
if($patient_id)
  $patient->updateNomPaysInsee();

$group = CGroups::loadCurrent();
$group->loadConfigValues();
$nom_jeune_fille_mandatory = $group->_configs['dPpatients_CPatient_nom_jeune_fille_mandatory'];

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->assign("useVitale", $useVitale);
$smarty->assign('nom_jeune_fille_mandatory', $nom_jeune_fille_mandatory);
$smarty->display("vw_edit_patients.tpl");
