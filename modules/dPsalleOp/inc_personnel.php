<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 1124 $
* @author Alexis Granger
*/

function loadAffectations(&$selOp, &$tabPersonnel, &$listPersAideOp, &$listPersPanseuse, &$timingAffect){
	
  // Chargement du personnel "aide_operatoire"
  $listPersAideOp = CPersonnel::loadListPers("op");

  // Chargement du personnel "op_panseuse"
  $listPersPanseuse = CPersonnel::loadListPers("op_panseuse");
  
    
  // Chargement des affectations de la plageOp
  $selOp->_ref_plageop->loadAffectationsPersonnel();
  
	$affectations_plage_op = $selOp->_ref_plageop->_ref_affectations_personnel["op"];
	$affectations_plage_panseuse = $selOp->_ref_plageop->_ref_affectations_personnel["op_panseuse"];
	
	if(!$affectations_plage_op){
	  $affectations_plage_op = array();
	}
	if(!$affectations_plage_panseuse){
	  $affectations_plage_panseuse = array();
	}
	$affectations_plage = array_merge($affectations_plage_op, $affectations_plage_panseuse);
  
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
		  $tabPersonnel["plage"][$affectation_personnel->_ref_personnel->_id] = $affectation;
		}
	}	
		// Chargement du de l'operation
		$selOp->loadAffectationsPersonnel();
		
		$affectations_operation_op = $selOp->_ref_affectations_personnel["op"];
		$affectations_operation_panseuse = $selOp->_ref_affectations_personnel["op_panseuse"];
		
		if(!$affectations_operation_op){
		  $affectations_operation_op = array();
		}
	  if(!$affectations_operation_panseuse){
		  $affectations_operation_panseuse = array();
		}
		
		$affectations_operation = array_merge($affectations_operation_op, $affectations_operation_panseuse);
	
		foreach($affectations_operation as $key => $affectation_personnel){
		// Si le personnel n'est pas deja present dans le tableau d'affectation, on le rajoute
      if((!array_key_exists($affectation_personnel->_ref_personnel->_id, $tabPersonnel["plage"])) 
      && ($affectation_personnel->_ref_personnel->emplacement == "op" || $affectation_personnel->_ref_personnel->emplacement == "op_panseuse")){
		    $affectation_personnel->_ref_personnel->loadRefUser();
		    $tabPersonnel["operation"][$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
		  }
		}
		
	  // Suppression de la liste des personnels deja presents
		foreach($listPersAideOp as $key => $pers){
		  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
		    unset($listPersAideOp[$key]);
		  }
		}
		
		// Suppression de la liste des personnels deja presents
		foreach($listPersPanseuse as $key => $pers){
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
	
}

?>