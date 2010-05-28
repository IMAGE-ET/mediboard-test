<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

$pat_id    = CValue::getOrSession("pat_id");
$sejour_id = CValue::getOrSession("sejour_id");

// Chargement du dossier patient
$patient = new CPatient;
$patient->load($pat_id);

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$isSejourPatient = null;
if ($patient->_id) {
	$patient->loadRefsFwd();
	$patient->loadRefPhotoIdentite();
  $patient->loadRefDossierMedical();
  $patient->_ref_dossier_medical->updateFormFields();
  $patient->loadRefsSejours();
  $patient->loadIPP();
  
  if (array_key_exists($sejour_id, $patient->_ref_sejours)) {
    $isSejourPatient = $sejour_id;
  }
  
  // Sejours
  foreach ($patient->_ref_sejours as $_sejour) {
    $_sejour->loadNumDossier();
    $_sejour->loadRefsOperations();
    $_sejour->canRead();
    $_sejour->canEdit();
    foreach ($_sejour->_ref_operations as $_operation) {
      $_operation->countDocItems();
      $_operation->canRead();
      $_operation->canEdit();
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("hprim21installed", CModule::getActive("hprim21"));

$smarty->assign("patient"        , $patient);
$smarty->assign("isSejourPatient", $isSejourPatient);
$smarty->assign("listPrat"       , $listPrat);

$smarty->display("vw_dossier.tpl");

?>