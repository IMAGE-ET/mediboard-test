<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
    return;
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}

global $AppUI;

$prescription_id = mbGetValueFromPost("prescription_id");
$type = mbGetValueFromPost("type");

// Chargement de la prescription à dupliquer
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefObject();
$prescription->loadRefsLines();

$sejour = $prescription->_ref_object;

// Creation de la nouvelle prescription
$prescription->_id = "";
$prescription->type = $type;
$prescription->praticien_id = $AppUI->user_id;
$msg = $prescription->store();
viewMsg($msg, "msg-CPrescription-create");
if($msg){
	$AppUI->redirect("m=dPprescription&a=vw_edit_prescription&popup=1&dialog=1&prescription_id=".$prescription_id);
}

// Parcours des lignes de prescription
foreach($prescription->_ref_prescription_lines as $line){

	// Chargements des lignes de posologie
  $line->loadRefsPrises();
	
	// Si la prescription à créer est sejour et qu'on a les infos sur la duree de la ligne
	if($type == "sejour" && $line->debut && $line->duree){
		// si la fin de la ligne est inferieur a l'entree, on ne la sauvegarde pas
    if($line->date_arret){
      $line->_fin = $line->date_arret;	
    }
		if($line->_fin < mbDate($sejour->_entree)){
    	continue;
    }
    // On ajuste la date d'entree et la duree
		if($line->debut < mbDate($sejour->_entree)){
			$diff_duree = mbDaysRelative($line->debut, mbDate($sejour->_entree));
			$line->duree = $line->duree - $diff_duree;
			$line->debut = mbDate($sejour->_entree);
		}
	}
	
	if($type == "sortie" && $line->debut && $line->duree){
		// si le debut de la ligne est apres la fin du sejour, on ne sauvegarde pas la ligne
    if($line->date_arret){
      $line->_fin = $line->date_arret;	
    }
		if($line->_fin < mbDate($sejour->_sortie)){
    	continue;
    }
    if($line->debut < mbDate($sejour->_sortie)){
    	$diff_duree = mbDaysRelative($line->debut, mbDate($sejour->_sortie));
    	$line->duree = $line->duree - $diff_duree;
    	$line->debut = mbDate($sejour->_sortie);
    }
	}
	
  $line->_id = "";
  $line->prescription_id = $prescription->_id;
  $line->praticien_id = $AppUI->user_id;
  $line->signee = 0;
  $line->valide_pharma = 0;
  
  $msg = $line->store();
  viewMsg($msg, "msg-CPrescriptionLineMedicament-create");  
  
	// Parcours des prises et creation des nouvelles prises
	foreach($line->_ref_prises as $prise){
		$prise->_id = "";
		$prise->object_id = $line->_id;
		$msg = $prise->store();
	  viewMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

$AppUI->redirect("m=dPprescription&a=vw_edit_prescription&popup=1&dialog=1&prescription_id=".$prescription->_id);

?>