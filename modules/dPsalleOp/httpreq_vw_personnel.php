<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 
* @author Alexis Granger
*/

global $can, $m;

CAppUI::requireModuleFile("dPsalleOp", "inc_personnel");

$date  = CValue::getOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

// Chargement de l'operation selectionnee
$operation_id = CValue::getOrSession("operation_id");
$selOp = new COperation();
$selOp->load($operation_id);
$selOp->loadRefPlageOp();

// Creation du tableau d'affectation de personnel
$tabPersonnel = array();

// Creation du tableau de timing pour les affectations  
$timingAffect = array();
  
// Chargement des affectations de personnel pour la plageop et l'intervention
loadAffectations($selOp, $tabPersonnel, $listPersAideOp, $listPersPanseuse, $timingAffect);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp"           , $selOp           );
$smarty->assign("tabPersonnel"    , $tabPersonnel    );
$smarty->assign("listPersAideOp"  , $listPersAideOp  );
$smarty->assign("listPersPanseuse", $listPersPanseuse);
$smarty->assign("timingAffect"    , $timingAffect    );
$smarty->assign("modif_operation" , $modif_operation );

$smarty->display("inc_vw_personnel.tpl");

?>