<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$date = mbGetValueFromGetOrSession("date");

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

$prises = array();
$lines_med = array();
$medsNonPresc = array();


if($prescription->_id){
  $prescription->loadRefsLines();
 
	foreach($prescription->_ref_prescription_lines as &$line){
		if($line->date_arret && $line->date_arret < $date){
			continue;
		}
		
	  if($date >= $line->debut && $date <= $line->_fin){
	  	$line->loadRefsPrises();  	
	  	foreach($line->_ref_prises as $prise){
	  	  if($prise->nb_tous_les && $prise->unite_tous_les){
	  		  if($prise->calculDatesPrise($date)){  	
	  		  	$prises[$line->_id][] = $prise;
	  		  } else {
	  		  	// liste des medicaments contenant des prises non prescrites pour la journée courante 
	  		  	$medsNonPresc[$line->_id] = $line; 
	  		  }
	      } else {
	      	$prises[$line->_id][] = $prise;
	      }
	  	}
	  	$lines_med[$line->_id] = $line;
	  }  
	}
}


foreach($medsNonPresc as $_line){
	// Si le medicament ne possede pas de prises
	if(!array_key_exists($_line->_id, $prises)){
		unset($lines_med[$_line->_id]);
	}
}



// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prises", $prises);
$smarty->assign("lines_med", $lines_med);
$smarty->display("inc_vw_dossier_soins.tpl");

?>