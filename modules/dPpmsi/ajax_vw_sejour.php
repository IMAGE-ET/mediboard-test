<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$sejour_id = CValue::getOrSession("sejour_id");

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefPatient();

$patient = $sejour->_ref_patient;
$patient->loadRefsFwd();
$patient->loadRefPhotoIdentite();
$patient->loadRefDossierMedical();
$patient->_ref_dossier_medical->updateFormFields();
$patient->_ref_dossier_medical->loadRefsAntecedents();
$patient->_ref_dossier_medical->loadRefsTraitements();
$patient->loadIPP();

$sejour->loadRefDossierMedical();
$sejour->_ref_dossier_medical->updateFormFields();
$sejour->_ref_dossier_medical->loadRefsAntecedents();
$sejour->_ref_dossier_medical->loadRefsTraitements();
$sejour->loadRefsAffectations();
$sejour->loadExtDiagnostics();
$sejour->loadRefs();
$sejour->countEchangeHprim();
$sejour->loadRefGHM();
$sejour->loadNumDossier();
$sejour->canRead();
$sejour->canEdit();
foreach ($sejour->_ref_operations as $_operation) {
  $_operation->loadRefsFwd();
  $_operation->countEchangeHprim();
  $_operation->countDocItems();
  $_operation->loadRefsActesCCAM();
  $_operation->canRead();
  $_operation->canEdit();
  foreach ($_operation->_ref_actes_ccam as $_acte) {
    $_acte->loadRefsFwd();
    $_acte->guessAssociation();
  }
  if($_operation->plageop_id) {
    $plage =& $_operation->_ref_plageop;
    $plage->loadRefsFwd();
  }
  
  $consultAnest =& $_operation->_ref_consult_anesth;
  if ($consultAnest->consultation_anesth_id) {
    $consultAnest->loadRefsFwd();
    $consultAnest->_ref_plageconsult->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("hprim21installed", CModule::getActive("hprim21"));
$smarty->assign("sejour" , $sejour );
$smarty->assign("listPrat", $listPrat);

$smarty->display("inc_vw_sejour.tpl");

?>