<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$user = new CMediusers();
$user->load($AppUI->user_id);

if(!$user->isPraticien()) {
  $can->needsRead();
}

$sejour_id = CValue::get("sejour_id", 0);
$user_id = CValue::get("user_id");
$cible = CValue::get("cible");
// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_class = "CSejour";
$prescription->object_id = $sejour_id;
$prescription->type = "sejour";
$prescription->loadMatchingObject();

$observation  = new CObservationMedicale();
$transmission = new CTransmissionMedicale();

$observation->loadAides($AppUI->user_id);
$transmission->loadAides($AppUI->user_id);

// Chargement du sejour
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadSuiviMedical();
$sejour->loadRefPraticien();

$cibles = array();
$users = array();
foreach($sejour->_ref_suivi_medical as $_trans_or_obs) {
  $users[$_trans_or_obs->user_id] = $_trans_or_obs->_ref_user;
  $type = ($_trans_or_obs instanceof CObservationMedicale) ? "obs" : "trans";
  if($user_id && $_trans_or_obs->user_id != $user_id){
    unset($sejour->_ref_suivi_medical[$_trans_or_obs->date.$_trans_or_obs->_id.$type]);
  }
	
  $_trans_or_obs->loadRefUser();
  if($_trans_or_obs instanceof CTransmissionMedicale) {
    $trans = $_trans_or_obs;
    $trans->calculCibles($cibles);
		if ($cible && $_trans_or_obs->_cible != $cible){
			unset($sejour->_ref_suivi_medical[$_trans_or_obs->date.$_trans_or_obs->_id.$type]);
		}
  }
}

//Ajout des constantes
$constantes = new CConstantesMedicales();
$constantes->patient_id = $sejour->patient_id;
$constantes = $constantes->loadMatchingList();

//mettre les transmissions dans un tableau dont l'index est le datetime 

// rechercher le user
foreach($constantes as $cst) {
  $user_ref_view = "";
	$user_ref_id = "";
  $cst->loadLastLog();
  if ( $cst->_ref_last_log) {
    $log = $cst->_ref_last_log;
		$log->loadRefsFwd();
  }
  if($log->_ref_user) {
    $user_ref_view = ($log->_ref_user->_view);
		$user_ref_id = ($log->_ref_user->_id);
  }
  $cst->_ref_user = $user_ref_view;
	$cst->_ref_user_id = $user_ref_id;
	if($user_id && $cst->_ref_user_id != $user_id) {
		unset($constantes[$cst->_id]);
	}
}
if(!$cible){
  $sejour->_ref_suivi_medical = array_merge($constantes,$sejour->_ref_suivi_medical);
}

//mettre les transmissions dans un tableau dont l'index est le datetime 
$list_trans_const = array();
foreach($sejour->_ref_suivi_medical as $_trans_const) {
	if($_trans_const instanceof CConstantesMedicales) {
		$list_trans_const["$_trans_const->datetime.$_trans_const->_id"] = $_trans_const;
	}
	else {
		$list_trans_const["$_trans_const->date.$_trans_const->_id"] = $_trans_const;
	}
}

krsort($list_trans_const);
$count_trans = count($list_trans_const);
$sejour->_ref_suivi_medical = $list_trans_const;
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("params"              , CConstantesMedicales::$list_constantes);
$smarty->assign("page_step"           , 10);
$smarty->assign("count_trans"         , $count_trans);
$smarty->assign("observation"         , $observation);
$smarty->assign("transmission"        , $transmission);
$smarty->assign("user"                , $user);
$smarty->assign("isPraticien"         , $user->isPraticien());
$smarty->assign("sejour"              , $sejour);
$smarty->assign("prescription"        , $prescription);
$smarty->assign("cibles"              , $cibles);
$smarty->assign("cible"               , $cible);
$smarty->assign("users"               , $users);
$smarty->assign("user_id"             , $user_id);
$smarty->assign("date"                , mbDate());
$smarty->assign("hour"                , mbTransformTime(null, mbTime(), "%H"));
$smarty->display("inc_vw_dossier_suivi.tpl");

?>