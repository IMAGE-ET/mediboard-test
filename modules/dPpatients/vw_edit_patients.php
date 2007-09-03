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

$date = mbDate();
$dateCMU = mbDate("+1 year", $date);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
}

// Module de carte vitale

$intermaxFunctions = array(
  "Lire Vitale",
);

if (mbGetValueFromGet("useVitale")) {
  $patient->getValuesFromVitale();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dateCMU", $dateCMU);
$smarty->assign("patient", $patient);

$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->assign("newLine"          , "---");

$smarty->display("vw_edit_patients.tpl");
?>