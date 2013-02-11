<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 
* @author Alexis Granger
*/

$date     = CValue::getOrSession("date", mbDate());
$in_salle = CValue::get("in_salle", 1);
$modif_operation = CCAnDo::edit() || $date >= mbDate();

// Chargement de l'operation selectionnee
$operation_id = CValue::getOrSession("operation_id");
$selOp = new COperation();
$selOp->load($operation_id);
$selOp->loadRefPlageOp();

// Creation du tableau d'affectation de personnel
$tabPersonnel = array();

// Chargement de la liste du personnel  
$listPersIADE     = CPersonnel::loadListPers("iade");
$listPersAideOp   = CPersonnel::loadListPers("op");
$listPersPanseuse = CPersonnel::loadListPers("op_panseuse");

// Chargement des affectations de la plageOp
$plageOp = $selOp->_ref_plageop;
$plageOp->loadAffectationsPersonnel();
$affectations_personnel = $plageOp->_ref_affectations_personnel;

$affectations_plage = array_merge(
  $affectations_personnel["iade"],
  $affectations_personnel["op"],
  $affectations_personnel["op_panseuse"]
);

// Tableau de stockage des affectations
$tabPersonnel["plage"] = array();
$tabPersonnel["operation"] = array();

foreach ($affectations_plage as $key => $affectation_personnel) {
  $affectation = new CAffectationPersonnel();
  $affectation->object_class = "COperation";
  $affectation->object_id    = $selOp->_id;
  $affectation->personnel_id = $affectation_personnel->_ref_personnel->_id;
  $affectation->loadMatchingObject();
  $affectation->loadRefPersonnel();
  $affectation->_ref_personnel->loadRefUser();
  $affectation->_ref_personnel->_ref_user->loadRefFunction();
  $tabPersonnel["plage"][$affectation_personnel->_ref_personnel->_id] = $affectation;
}

// Chargement du de l'operation
$selOp->loadAffectationsPersonnel();
$affectations_personnel = $selOp->_ref_affectations_personnel;

$affectations_operation = array_merge(
  $affectations_personnel["iade"],
  $affectations_personnel["op"],
  $affectations_personnel["op_panseuse"]
);

foreach ($affectations_operation as $key => $affectation_personnel) {
  // Si le personnel n'est pas deja present dans le tableau d'affectation, on le rajoute
  if (
      (!array_key_exists($affectation_personnel->_ref_personnel->_id, $tabPersonnel["plage"]))
      && ($affectation_personnel->_ref_personnel->emplacement == "op" ||
      $affectation_personnel->_ref_personnel->emplacement == "op_panseuse" ||
      $affectation_personnel->_ref_personnel->emplacement == "iade")
  ) {
    $tabPersonnel["operation"][$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
  }
}

// Suppression de la liste des personnels deja presents
foreach ($listPersIADE as $key => $pers) {
  if (array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])) {
    unset($listPersIADE[$key]);
  }
}
foreach ($listPersAideOp as $key => $pers) {
  if (array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])) {
    unset($listPersAideOp[$key]);
  }
}
foreach ($listPersPanseuse as $key => $pers) {
  if (array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])) {
    unset($listPersPanseuse[$key]);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp"           , $selOp);
$smarty->assign("tabPersonnel"    , $tabPersonnel);
$smarty->assign("listPersIADE"    , $listPersIADE);
$smarty->assign("listPersAideOp"  , $listPersAideOp);
$smarty->assign("listPersPanseuse", $listPersPanseuse);
$smarty->assign("modif_operation" , $modif_operation);
$smarty->assign("in_salle"        , $in_salle);

$smarty->display("inc_vw_personnel.tpl");
