<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 
* @author Alexis Granger
*/

global $can, $m;

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
$timingAffect = array();

// Chargement de la liste du personnel  
$listPersIADE = CPersonnel::loadListPers("iade");
$listPersAideOp = CPersonnel::loadListPers("op");
$listPersPanseuse = CPersonnel::loadListPers("op_panseuse");

// Chargement des affectations de la plageOp
$selOp->_ref_plageop->loadAffectationsPersonnel();

$affectations_plage_iade = $selOp->_ref_plageop->_ref_affectations_personnel["iade"];
$affectations_plage_op = $selOp->_ref_plageop->_ref_affectations_personnel["op"];
$affectations_plage_panseuse = $selOp->_ref_plageop->_ref_affectations_personnel["op_panseuse"];

if(!$affectations_plage_iade){
  $affectations_plage_iade = array();
}
if(!$affectations_plage_op){
  $affectations_plage_op = array();
}
if(!$affectations_plage_panseuse){
  $affectations_plage_panseuse = array();
}
$affectations_plage = array_merge($affectations_plage_iade, $affectations_plage_op, $affectations_plage_panseuse);

// Tableau de stockage des affectations
$tabPersonnel["plage"] = array();
$tabPersonnel["operation"] = array();

if($affectations_plage){
  foreach($affectations_plage as $key => $affectation_personnel){
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
} 
  
// Chargement du de l'operation
$selOp->loadAffectationsPersonnel();

$affectations_operation_iade = $selOp->_ref_affectations_personnel["iade"];
$affectations_operation_op = $selOp->_ref_affectations_personnel["op"];
$affectations_operation_panseuse = $selOp->_ref_affectations_personnel["op_panseuse"];

if(!$affectations_operation_iade){
  $affectations_operation_iade = array();
}
if(!$affectations_operation_op){
  $affectations_operation_op = array();
}
if(!$affectations_operation_panseuse){
  $affectations_operation_panseuse = array();
}

$affectations_operation = array_merge($affectations_operation_iade, $affectations_operation_op, $affectations_operation_panseuse);

foreach($affectations_operation as $key => $affectation_personnel){
  // Si le personnel n'est pas deja present dans le tableau d'affectation, on le rajoute
  if((!array_key_exists($affectation_personnel->_ref_personnel->_id, $tabPersonnel["plage"])) 
  && ($affectation_personnel->_ref_personnel->emplacement == "op" || $affectation_personnel->_ref_personnel->emplacement == "op_panseuse" || $affectation_personnel->_ref_personnel->emplacement == "iade")){
    $affectation_personnel->_ref_personnel->loadRefUser();
		$affectation_personnel->_ref_personnel->_ref_user->loadRefFunction();
    $tabPersonnel["operation"][$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
  }
}

// Suppression de la liste des personnels deja presents
foreach($listPersIADE as $key => $pers){
	$pers->_ref_user->loadRefFunction();
  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
    unset($listPersIADE[$key]);
  }
}
foreach($listPersAideOp as $key => $pers){
  $pers->_ref_user->loadRefFunction();
  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
    unset($listPersAideOp[$key]);
  }
}
foreach($listPersPanseuse as $key => $pers){
  $pers->_ref_user->loadRefFunction();
  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
    unset($listPersPanseuse[$key]);
  }
}

// Initialisations des tableaux de timing
foreach($tabPersonnel as $key_type => $type_affectation){
  foreach($type_affectation as $key => $affectation){
    $timingAffect[$affectation->_id]["_debut"] = array();
    $timingAffect[$affectation->_id]["_fin"] = array();
  }
}

// Remplissage tu tableau de timing
foreach($tabPersonnel as $cle => $type_affectation){
  foreach($type_affectation as $cle_type =>$affectation){
    foreach($timingAffect[$affectation->_id] as $key => $value){
      for($i = -10; $i < 10 && $affectation->$key !== null; $i++) {
        $timingAffect[$affectation->_id][$key][] = mbTime("$i minutes", $affectation->$key);
      }  
    } 
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("selOp"           , $selOp           );
$smarty->assign("tabPersonnel"    , $tabPersonnel    );
$smarty->assign("listPersIADE"    , $listPersIADE  );
$smarty->assign("listPersAideOp"  , $listPersAideOp  );
$smarty->assign("listPersPanseuse", $listPersPanseuse);
$smarty->assign("timingAffect"    , $timingAffect    );
$smarty->assign("modif_operation" , $modif_operation );
$smarty->display("inc_vw_personnel.tpl");

?>