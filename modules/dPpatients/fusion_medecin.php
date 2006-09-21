<?php /* $Id: fusion_pat.php 331 2006-07-13 14:26:26Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 331 $
* @author Romain Ollivier
*/

global $AppUI, $m;

$listIds = array();
foreach($_GET as $key => $value) {
  if(strpos($key, "fusion_") !== false)
    $listIds[] = substr($key, 7);
}
if(count($listIds) < 2) {
  $AppUI->setMsg("Veuillez selectionner deux medecins", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=vw_medecins");
}

// Instance des patients
$medecin1 = new CMedecin;
$medecin1->load($listIds[0]);
$medecin1->loadRefsFwd();

$medecin2 = new CMedecin;
$medecin2->load($listIds[1]);
$medecin2->loadRefsFwd();

// On base le résultat sur patient1
$finalMedecin = new CMedecin;
$finalMedecin->load($listIds[0]);
$finalMedecin->loadRefsFwd();
$finalMedecin->medecin_id = null;

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("medecin1"    , $medecin1    );
$smarty->assign("medecin2"    , $medecin2    );
$smarty->assign("finalMedecin", $finalMedecin);

$smarty->display("fusion_medecin.tpl");
?>