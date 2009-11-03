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

$cibles = array();
$users = array();
foreach($sejour->_ref_suivi_medical as $_trans_or_obs){
  $users[$_trans_or_obs->user_id] = $_trans_or_obs->_ref_user;
  $type = ($_trans_or_obs->_class_name == "CObservationMedicale") ? "obs" : "trans";
  if($user_id && $_trans_or_obs->user_id != $user_id){
    unset($sejour->_ref_suivi_medical[$_trans_or_obs->date.$_trans_or_obs->_id.$type]);
  }
  $_trans_or_obs->loadRefUser();
  if($_trans_or_obs instanceof CTransmissionMedicale){
    $trans = $_trans_or_obs;
    $trans->calculCibles($cibles);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("observation"         , $observation);
$smarty->assign("transmission"        , $transmission);
$smarty->assign("user"                , $user);
$smarty->assign("isPraticien"         , $user->isPraticien());
$smarty->assign("sejour"              , $sejour);
$smarty->assign("prescription"        , $prescription);
$smarty->assign("cibles", $cibles);
$smarty->assign("users", $users);
$smarty->assign("user_id", $user_id);
$smarty->display("inc_vw_dossier_suivi.tpl");

?>