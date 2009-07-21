<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$today = date("d/m/Y");

$patient_id = mbGetValueFromGet("patient_id", 0);

//Création du patient
$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefs();

foreach($patient->_ref_sejours as $key => $sejour) {
  $patient->_ref_sejours[$key]->loadRefsFwd();
  $patient->_ref_sejours[$key]->loadRefsOperations();
  foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
    $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
  }
}
foreach($patient->_ref_consultations as $key => $value) {
  $patient->_ref_consultations[$key]->loadRefsFwd();
  $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("today"  , $today  );

if (CAppUI::conf('dPpatients CPatient extended_print'))
  $smarty->display("print_patient_extended.tpl");
else
  $smarty->display("print_patient.tpl");

?>