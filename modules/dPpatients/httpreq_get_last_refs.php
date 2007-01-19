<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$patient_id = mbGetValueFromGet("patient_id", 0);
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefs();
foreach($patient->_ref_sejours as $key => $sejour) {
  $patient->_ref_sejours[$key]->loadRefsOperations();
  foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
    $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
  }
}
foreach($patient->_ref_consultations as $key => $consult) {
  $patient->_ref_consultations[$key]->loadRefsFwd();
  $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefsFwd();
}

if ($canRead) {
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("patient", $patient);

  $smarty->display("httpreq_get_last_refs.tpl");
}

?>