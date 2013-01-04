<?php /* $Id $ */

/**
 * Vue dossier
 *
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$NDA = CValue::getOrSession("NDA");

if ($NDA) {
  $sejour = new CSejour();
  $sejour->loadFromNDA($NDA);

  if ($sejour->_id) {
    $sejour->loadRefPatient();

    CValue::setSession("pat_id"   , $sejour->_ref_patient->_id);
    CValue::setSession("sejour_id", $sejour->_id);

    unset($_GET["pat_id"]);
    unset($_GET["sejour_id"]);
  }
}

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
  $patient->loadRefsCorrespondants();
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
    $_sejour->loadNDA();
    $_sejour->loadRefsOperations();
    $_sejour->canDo();
    foreach ($_sejour->_ref_operations as $_operation) {
      $_operation->countDocItems();
      $_operation->canDo();
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet"));

$smarty->assign("hprim21installed", CModule::getActive("hprim21"));

$smarty->assign("patient"         , $patient);
$smarty->assign("isSejourPatient" , $isSejourPatient);
$smarty->assign("listPrat"        , $listPrat);

$smarty->assign("NDA"             , $NDA);

$smarty->display("vw_dossier.tpl");