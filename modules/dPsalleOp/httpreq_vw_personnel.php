<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;

// Chargement de la liste du personnel pour l'operation
$listPers = CPersonnel::loadListPers("op");

$date  = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

$tabPersonnel = array();
// Chargement de l'operation selectionnee
$operation_id = mbGetValueFromGetOrSession("operation_id");
$selOp = new COperation();
$selOp->load($operation_id);
$selOp->loadRefPlageOp();

// Creation du tableau de timing pour les affectations  
$timingAffect = array();
  
  
// Chargement du personnel de la plageop
// Chargement des affectation du personne
$selOp->_ref_plageop->loadPersonnel();
if ($selOp->_ref_plageop->_ref_personnel) {
	$tabPersonnel["plage"] = array();
	foreach($selOp->_ref_plageop->_ref_personnel as $key => $affectation_personnel){
	  // Chargement du personnel a partir des affectations      
	  //  $tabPersonnel[$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
	  $affectation = new CAffectationPersonnel();
	  $affectation->object_class = "COperation";
	  $affectation->object_id    = $selOp->_id;
	  $affectation->personnel_id = $affectation_personnel->_ref_personnel->_id;
	  $affectation->loadMatchingObject();
	  $affectation->loadPersonnel();
	  $affectation->_ref_personnel->loadRefUser();
	  $tabPersonnel["plage"][$affectation_personnel->_ref_personnel->_id] = $affectation;
	}
	
	// Chargement du personnel non present dans la plageop (rajouté dans l'operation)
	$selOp->loadPersonnel();
	$tabPersonnel["operation"] = array();
	foreach($selOp->_ref_personnel as $key => $affectation_personnel){
	// Si le personnel n'est pas deja present dans le tableau d'affectation, on le rajoute
	  if(!array_key_exists($affectation_personnel->_ref_personnel->_id, $tabPersonnel["plage"])){
	    $affectation_personnel->_ref_personnel->loadRefUser();
	    $tabPersonnel["operation"][$affectation_personnel->_ref_personnel->_id] = $affectation_personnel; 
	  }
	}
	
	// Suppression de la liste des personnels deja presents
	foreach($listPers as $key => $pers){
	  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
	   unset($listPers[$key]);
	  }
	}
	
	// Initialisation des tableaux de timing
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
}
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp"           , $selOp           );
$smarty->assign("tabPersonnel"    , $tabPersonnel    );
$smarty->assign("listPers"        , $listPers        );
$smarty->assign("timingAffect"    , $timingAffect    );
$smarty->assign("modif_operation" , $modif_operation );

$smarty->display("inc_vw_personnel.tpl");

?>