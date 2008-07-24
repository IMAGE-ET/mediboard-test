<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$user = new CMediusers();
$user->load($AppUI->user_id);

if(!$user->isPraticien()) {
  $can->needsRead();
}

$sejour_id = mbGetValueFromGet("sejour_id", 0);

$observation  = new CObservationMedicale();
$transmission = new CTransmissionMedicale();

$observation->loadAides($AppUI->user_id);
$transmission->loadAides($AppUI->user_id);

// Chargement du sejour
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadBackRefs("observations");
$sejour->loadBackRefs("transmissions");

$sejour->_ref_suivi_medical = array();
foreach($sejour->_back["observations"] as $curr_obs) {
  $curr_obs->loadRefsFwd();
 
  $curr_obs->_ref_user->loadRefFunction();
  $sejour->_ref_suivi_medical[$curr_obs->date.$curr_obs->_id."obs"] = $curr_obs;
}
foreach($sejour->_back["transmissions"] as $curr_trans) {
  $curr_trans->loadRefsFwd();
  $sejour->_ref_suivi_medical[$curr_trans->date.$curr_trans->_id."trans"] = $curr_trans;
}

krsort($sejour->_ref_suivi_medical);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("observation"         , $observation);
$smarty->assign("transmission"        , $transmission);
$smarty->assign("user"                , $user);
$smarty->assign("isPraticien"         , $user->isPraticien());
$smarty->assign("sejour"              , $sejour);

$smarty->display("inc_vw_dossier_suivi.tpl");

?>