<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $can;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$name       = mbGetValueFromGet("name");
$firstName  = mbGetValueFromGet("firstName");

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
}

  // Remplissage des valeurs un patient vitale
if (mbGetValueFromGet("useVitale")) {
  $patient->getValuesFromVitale();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->display("vw_edit_patients.tpl");
?>