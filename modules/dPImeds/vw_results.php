<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m, $g, $dPconfig;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
if($sejour_id) {
  if(isset($patient->_ref_sejours[$sejour_id])) {
    $sejour =& $patient->_ref_sejours[$sejour_id];
  } else {
    mbSetValueToSession("sejour_id");
    $sejour = new CSejour;
  }
} else {
  $sejour = new CSejour;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour );
$smarty->assign("url"    , $dPconfig["dPImeds"]["url"]);

$smarty->display("vw_results.tpl");

?>