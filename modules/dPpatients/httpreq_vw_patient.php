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
  $patient->loadPatientLinks();
  if (CModule::getActive("fse")) {
    $cv = CFseFactory::createCV();
    if ($cv) {
      $cv->loadIdVitale($patient);
    }
  }
}

$vip = 0;
if($patient->vip && !CCanDo::admin()) {
	$user_in_list_prat = false;
  $user_in_logs      = false;
  foreach($patient->_ref_praticiens as $_prat) {
		if($user->_id == $_prat->user_id) {
      $user_in_list_prat = true;
    }
  }
  $patient->loadLogs();
  foreach($patient->_ref_logs as $_log) {
    if($user->_id == $_log->user_id) {
      $user_in_logs = true;
    }
  }
  $vip = !$user_in_list_prat && !$user_in_logs;
}

if($vip) {
	CValue::setSession("patient_id", 0);
}

$listPrat = $user->loadPraticiens(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient"         , $patient);
$smarty->assign("vip"             , $vip);
$smarty->assign("listPrat"        , $listPrat);
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet"));
$smarty->display("inc_vw_patient.tpl");
?>