<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();
$patient_id = CValue::get("patient_id");

$patient = new CPatient;
$patient->load($patient_id);

$user = new CMediusers;
$user->load($AppUI->user_id);
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPrat = $user->loadPraticiens(PERM_EDIT);
} else {
  $listPrat = $user->loadProfessionnelDeSante(PERM_EDIT);
}

$patient->loadRefsFiles();
$patient->loadRefsDocs();
$where = array();
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$patient->loadRefsConsultations($where);
$patient->loadRefsSejours();

$patient->loadRefDossierMedical();

$patient->_ref_dossier_medical->loadRefsAntecedents();
$patient->_ref_dossier_medical->loadRefsTraitements();

$consultations =& $patient->_ref_consultations;
$sejours =& $patient->_ref_sejours;

// Consultations
foreach ($consultations as &$consultation) {
  $consultation->loadRefsBack();
  $consultation->loadRefsReglements();
  $consultation->loadRefPlageConsult();
	$consultation->_ref_plageconsult->_ref_chir->loadRefFunction();
}

// Sejours
$where = array();
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
foreach ($patient->_ref_sejours as &$sejour) {
  $sejour->loadRefsOperations($where);
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->loadRefPlageOp();
    $operation->loadRefChir();
    $operation->loadRefsFiles();
    $operation->loadRefsDocs();
    $operation->loadExtCodesCCAM();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);

$smarty->display("vw_resume.tpl");

?>