<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

$today = date("d/m/Y");

// Récupération des variables passées en GET
$patient_id = dPgetParam($_GET, "patient_id", 0);

//Création du patient
$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefs();

foreach($patient->_ref_operations as $key => $value) {
  $patient->_ref_operations[$key]->loadRefsFwd();
}
foreach($patient->_ref_consultations as $key => $value) {
  $patient->_ref_consultations[$key]->loadRefsFwd();
  $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefsFwd();
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;
$smarty->assign('patient', $patient);
$smarty->assign('today', $today);
$smarty->display('print_patient.tpl');

?>