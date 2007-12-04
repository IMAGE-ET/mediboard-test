<?php

/**
* @package Mediboard
* @subpackage dpSalleOp
* @version $Revision: 1124 $
* @author Alexis Granger
*/

function loadAffectations(&$selOp, &$tabPersonnel, &$listPers, &$timingAffect){
	$selOp->_ref_plageop->loadPersonnel();
	
	$tabPersonnel["plage"] = array();
	$tabPersonnel["operation"] = array();
	
	if($selOp->_ref_plageop->_ref_personnel){
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
		foreach($selOp->_ref_personnel as $key => $affectation_personnel){
		// Si le personnel n'est pas deja present dans le tableau d'affectation, on le rajoute
		  if(!array_key_exists($affectation_personnel->_ref_personnel->_id, $tabPersonnel["plage"])){
		    $affectation_personnel->_ref_personnel->loadRefUser();
		    $tabPersonnel["operation"][$affectation_personnel->_ref_personnel->_id] = $affectation_personnel;  
		  }
		}
		
		// Chargement de la liste du personnel pour l'operation
	  $listPers = CPersonnel::loadListPers("op");
	  
	  // Suppression de la liste des personnels deja presents
		foreach($listPers as $key => $pers){
		  if(array_key_exists($pers->_id, $tabPersonnel["plage"]) || array_key_exists($pers->_id, $tabPersonnel["operation"])){
		    unset($listPers[$key]);
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
}

?>