<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients", "patients"));

if (!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

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
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("patient", $patient);
$smarty->assign("today"  , $today  );

$smarty->display("print_patient.tpl");

?>