<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$listIds = array();
foreach($_GET as $key => $value) {
  if(strpos($key, "fusion_") !== false)
    $listIds[] = substr($key, 7);
}

if(count($listIds) < 2) {
  $AppUI->setMsg("Veuillez selectionner deux patients", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients");
}

// Instance des patients
$patient1 = new CPatient;
$patient2 = new CPatient;

if (!$patient1->load($listIds[0]) || !$patient2->load($listIds[1])){
  // Erreur sur les ID du patient
  $AppUI->setMsg("Fusion Impossible", UI_MSG_ERROR );
  $AppUI->redirect("m=dPpatients");
}


$patient1->loadRefsFwd();
$patient2->loadRefsFwd();
$testMerge = $patient1->checkMerge($patient1, $patient2);

// On base le résultat sur patient1
$finalPatient = new CPatient;
$finalPatient->load($listIds[0]);
$finalPatient->loadRefsFwd();
$finalPatient->patient_id = null;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient1"    , $patient1    );
$smarty->assign("patient2"    , $patient2    );
$smarty->assign("finalPatient", $finalPatient);
$smarty->assign("testMerge"   , $testMerge);

$smarty->display("fusion_pat.tpl");
?>