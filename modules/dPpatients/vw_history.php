<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;


$can->needsRead();

// Définition des variables
$patient_id = CValue::get("patient_id", 0);

//Récupération du dossier complet patient
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet();

$patient->loadLogs();

// log pour les séjours
foreach($patient->_ref_sejours as $sejour) {
  $sejour->loadLogs();
  
  // log pour les opérations de ce séjour
  $sejour->loadRefsOperations();
  foreach($sejour->_ref_operations as $operation) {
  	$operation->loadRefsFwd();
    $operation->loadLogs();
  }
  
  // log pour les affectations de ce séjour
  $sejour->loadRefsAffectations();  
  foreach($sejour->_ref_affectations as $affectation) {
    $affectation->loadLogs();
    $affectation->loadRefsFwd();
  }
}

// log pour les consultations
foreach($patient->_ref_consultations as $consultation) {
  $consultation->loadLogs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient" , $patient );

$smarty->display("vw_history.tpl");
?>