<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

$user = CAppUI::$user;

if(!$user->isPraticien()) {
  CCanDo::checkRead();
}

$sejour_id   = CValue::get("sejour_id", 0);
$user_id     = CValue::get("user_id");
$cible       = CValue::get("cible");
$_show_obs   = CValue::getOrSession("_show_obs", 1);
$_show_trans = CValue::getOrSession("_show_trans", 1);

// Chargement du sejour
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadSuiviMedical();
$sejour->loadRefPraticien();

$sejour->loadRefPrescriptionSejour();
$prescription =& $sejour->_ref_prescription_sejour;

$is_praticien = $user->isPraticien();

$cibles = array();
$users = array();

foreach($sejour->_ref_suivi_medical as $_suivi) {
  // Elements et commentaires
	if($_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment){
  	$_suivi->loadRefPraticien();
    $users[$_suivi->praticien_id] = $_suivi->_ref_praticien;
		if($user_id && $_suivi->praticien_id != $user_id){
      unset($sejour->_ref_suivi_medical["$_suivi->debut $_suivi->time_debut $_suivi->_guid"]);
	  }
  }	
	// Transmissions et Observations
	else {
  	$users[$_suivi->user_id] = $_suivi->_ref_user;
	  $type = ($_suivi instanceof CObservationMedicale) ? "obs" : "trans";
	  if($user_id && $_suivi->user_id != $user_id){
	    unset($sejour->_ref_suivi_medical[$_suivi->date.$_suivi->_id.$type]);
	  }
	  
	  $_suivi->loadRefUser();
	  if($_suivi instanceof CTransmissionMedicale) {
	    $trans = $_suivi;
	    $trans->calculCibles($cibles);
	    if ($cible && $_suivi->_cible != $cible){
	      unset($sejour->_ref_suivi_medical[$_suivi->date.$_suivi->_id.$type]);
	    }
	  }
	  $_suivi->canEdit();
  }
}

//TODO: Revoir l'ajout des constantes dans le suivi de soins 
//Ajout des constantes
if (!$cible && CAppUI::conf("soins constantes_show")){
  $sejour->loadRefConstantes($user_id);
}

//mettre les transmissions dans un tableau dont l'index est le datetime
$list_trans_const = array();
foreach($sejour->_ref_suivi_medical as $_trans_const) {
  if ($_trans_const instanceof CObservationMedicale && !$_show_obs) {
    continue;
  }
  if ($_trans_const instanceof CTransmissionMedicale && !$_show_trans) {
    continue;
  }
	if($_trans_const instanceof CConstantesMedicales) {
    $sort_key = "$_trans_const->datetime $_trans_const->_guid";
	} elseif ($_trans_const instanceof CTransmissionMedicale || $_trans_const instanceof CObservationMedicale){
		$sort_key = "$_trans_const->date $_trans_const->_guid";
	} else {
		$sort_key = "$_trans_const->debut $_trans_const->time_debut $_trans_const->_guid";
	}
	
	$list_trans_const[$sort_key] = $_trans_const;
}

krsort($list_trans_const);

$count_trans = count($list_trans_const);
$sejour->_ref_suivi_medical = $list_trans_const;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("params"      , CConstantesMedicales::$list_constantes);
$smarty->assign("page_step"   , 20);
$smarty->assign("readOnly"    , CValue::get("readOnly",false));
$smarty->assign("count_trans" , $count_trans);
$smarty->assign("user"        , $user);
$smarty->assign("isPraticien" , $is_praticien);
$smarty->assign("sejour"      , $sejour);
$smarty->assign("prescription", $prescription);
$smarty->assign("cibles"      , $cibles);
$smarty->assign("cible"       , $cible);
$smarty->assign("users"       , $users);
$smarty->assign("user_id"     , $user_id);
$smarty->assign("_show_obs"   , $_show_obs);
$smarty->assign("_show_trans" , $_show_trans);
$smarty->display("inc_vw_dossier_suivi.tpl");

?>