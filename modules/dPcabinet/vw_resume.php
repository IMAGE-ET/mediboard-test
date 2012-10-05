<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$patient_id = CValue::get("patient_id");

$patient = new CPatient;
$patient->load($patient_id);

$user = CMediusers::get();
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPrat = $user->loadPraticiens(PERM_EDIT, null, null, null, false);
} else {
  $listPrat = $user->loadProfessionnelDeSante(PERM_EDIT, null, null, null, false);
}

$patient->loadRefsFiles();
$patient->loadRefsDocs();
$where = array();
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$patient->loadRefsConsultations($where);
$patient->loadRefsSejours();

$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();
$prescription = $dossier_medical->loadRefPrescription();

if ($prescription && is_array($prescription->_ref_prescription_lines)) {
  foreach($dossier_medical->_ref_prescription->_ref_prescription_lines as $_line) {
    $_line->loadRefsPrises();
  }
}

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
  $sejour->loadRefPraticien();
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