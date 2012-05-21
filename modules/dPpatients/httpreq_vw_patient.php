<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkRead();
$user = CMediusers::get();

$patient_id = CValue::getOrSession("patient_id", 0);

// Récuperation du patient sélectionné
$patient = new CPatient();
if (CValue::get("new", 0)) {
  $patient->load(NULL);
  CValue::setSession("id", null);
} else {
  $patient->load($patient_id);
}

if ($patient->_id) {
  $patient->loadDossierComplet();
  $patient->loadIPP();
  if (CModule::getActive("fse")) {
    $cv = CFseFactory::createCV();
    if ($cv) {
      $cv->loadIdVitale($patient);
    }
  }
}

$listPrat = $user->loadPraticiens(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient"         , $patient);
$smarty->assign("listPrat"        , $listPrat);
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet"));
$smarty->display("inc_vw_patient.tpl");
?>