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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("observation"         , $observation);
$smarty->assign("transmission"        , $transmission);
$smarty->assign("user"                , $user);
$smarty->assign("isPraticien"         , $user->isPraticien());
$smarty->assign("sejour"              , $sejour);
$smarty->assign("prescription"        , $prescription);
$smarty->display("inc_vw_dossier_suivi.tpl");

?>