<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$patient_id = mbGetValueFromGet("patient_id");

$patient = new CPatient;
$patient->load($patient_id);

$user = new CMediusers;
$user->load($AppUI->user_id);
$listPrat = $user->loadPraticiens(PERM_EDIT);

$patient->loadRefsFiles();
$patient->loadRefsDocs();
$where = array();
$where["plageconsult.chir_id"] = $ds->prepareIn(array_keys($listPrat));
$patient->loadRefsConsultations($where);
$patient->loadRefsSejours();
$patient->loadRefsAntecedents();
$patient->loadRefsTraitements();

$consultations =& $patient->_ref_consultations;
$sejours =& $patient->_ref_sejours;

// Consultations
foreach ($patient->_ref_consultations as &$consultation) {
  $consultation->loadRefsBack();
  $consultation->loadRefPlageConsult();
}

// Sejours
$where = array();
$where["chir_id"] = $ds->prepareIn(array_keys($listPrat));
foreach ($patient->_ref_sejours as &$sejour) {
  $sejour->loadRefsOperations($where);
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->loadRefPlageOp();
    $operation->loadRefChir();
    $operation->loadRefsFiles();
    $operation->loadRefsDocs();
    $operation->loadRefsCodesCCAM();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"       , $patient);

$smarty->display("vw_resume.tpl");

?>