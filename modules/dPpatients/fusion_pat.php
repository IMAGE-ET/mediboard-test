<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPpatients", "patients"));

$listIds = array();
foreach($_GET as $key => $value) {
  if(strpos($key, "fusion_") !== false)
    $listIds[] = substr($key, 7);
}
if(count($listIds) < 2) {
  $AppUI->setMsg("Veuillez selectionner deux patients", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=vw_idx_patients");
}

// Instance des patients
$patient1 = new CPatient;
$patient1->load($listIds[0]);
$patient1->loadRefsFwd();

$patient2 = new CPatient;
$patient2->load($listIds[1]);
$patient2->loadRefsFwd();

// On base le résultat sur patient1
$finalPatient = new CPatient;
$finalPatient->load($listIds[0]);
$finalPatient->loadRefsFwd();
$finalPatient->patient_id = null;

$titleBlock = new CTitleBlock("Fusion de dossiers patient", "$m.png", $m, "$m.$a");
$titleBlock->addCell();
$titleBlock->show();

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("patient1"    , $patient1    );
$smarty->assign("patient2"    , $patient2    );
$smarty->assign("finalPatient", $finalPatient);

$smarty->display("fusion_pat.tpl");
?>