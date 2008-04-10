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

$old_prescription = new CPrescription();
$old_prescription->load($prescription_id);
$old_prescription->loadRefObject();
$sejour =& $old_prescription->_ref_object;

$new_prescription = $old_prescription;
$new_prescription->_id = "";
$new_prescription->type = $type;
$new_prescription->praticien_id = $AppUI->user_id;
$msg = $new_prescription->store();
viewMsg($msg, "msg-CPrescription-create");
if($msg){
	$AppUI->redirect("m=dPprescription&a=vw_edit_prescription&popup=1&dialog=1&prescription_id=".$prescription_id);
}

// Rechargement de la prescription
$old2_prescription = new CPrescription();
$old2_prescription->load($prescription_id);
$old2_prescription->loadRefsLines();

// Parcours des lignes de prescription
foreach($old2_prescription->_ref_prescription_lines as $line){
	$new_debut = "";
	$new_duree = "";
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
			$new_duree = $line->duree - $diff_duree;
			$new_debut = mbDate($sejour->_entree);
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
    	$new_duree = $line->duree - $diff_duree;
    	$new_debut = mbDate($sejour->_sortie);
    }
	}
	
  $new_line = new CPrescriptionLineMedicament();
  $new_line->_id = "";
  $new_line->code_cip = $line->code_cip;
  $new_line->no_poso = $line->no_poso;
  $new_line->commentaire = $line->commentaire;
  
  if($new_duree){
  	$new_line->duree = $new_duree;
  } else {
  	$new_line->duree = $line->duree;
  }
  
  if($new_debut){
  	$new_line->debut = $new_debut;
  } else {
    $new_line->debut = $line->debut;
  }
  
  $new_line->unite_duree = $line->unite_duree;
  $new_line->ald = $line->ald;
  $new_line->prescription_id = $new_prescription->_id;
  $new_line->praticien_id = $AppUI->user_id;
  $new_line->signee = $line->signee;
  $msg = $new_line->store();
  viewMsg($msg, "msg-CPrescriptionLineMedicament-create");  
  
  // Chargement des prises
  $line->loadRefsPrises();

	// Parcours des prises
	foreach($line->_ref_prises as $prise){
		$new_prise = new CPrisePosologie();
	  $new_prise->_id = "";
		$new_prise->prescription_line_id = $new_line->_id;
		$new_prise->moment_unitaire_id = $prise->moment_unitaire_id;
	  $new_prise->quantite = $prise->quantite;
	  $new_prise->nb_fois = $prise->nb_fois;
	  $new_prise->unite_fois = $prise->unite_fois;
	  $new_prise->nb_tous_les = $prise->nb_tous_les;
	  $new_prise->unite_tous_les = $prise->unite_tous_les;
	  $msg = $new_prise->store();
	  viewMsg($msg, "msg-CPrisePosologie-create");  	
	}
}

$AppUI->redirect("m=dPprescription&a=vw_edit_prescription&popup=1&dialog=1&prescription_id=".$new_prescription->_id);

?>