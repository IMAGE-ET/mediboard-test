<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

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
  $patient->loadIdVitale();
}

$user_in_list_prat = 0;
if($can->admin) {
	$user_in_list_prat = 0;
} elseif($patient->vip) {
	foreach($patient->_ref_praticiens as $_prat) {
		if($AppUI->user_id == $_prat->user_id) {
			$user_in_list_prat = 1;
		}
	}
}

$vip = $patient->vip && !$user_in_list_prat;
if($vip) {
	CValue::setSession("patient_id", 0);
}

$user = new CMediusers();
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