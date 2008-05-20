<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$date = mbGetValueFromGetOrSession("date");

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->loadMatchingObject();
$prescription_id = $prescription->_id;

$prises = array();
$prises_soin = array();
$lines_med = array();
$lines_soin = array();
$medsNonPresc = array();
$soinsNonPresc = array();


if($prescription->_id){
	// Chargement des lignes de medicaments
  $prescription->loadRefsLines();
  
  // Chargement des lignes de soins
  $prescription->loadRefsLinesElement("soin");
  $prescription->_ref_prescription_lines_element;
  
  // Parcours des medicaments
	foreach($prescription->_ref_prescription_lines as &$line){
		if($line->date_arret && $line->date_arret < $date){
			continue;
		}
		if(!$line->valide_pharma){
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
	
	// Parcours des soins
  foreach($prescription->_ref_prescription_lines_element as &$_soin){
		if($_soin->date_arret && $_soin->date_arret < $date){
			continue;
		}
	  if($date >= $_soin->debut && $date <= $_soin->_fin){
	  	$_soin->loadRefsPrises();  	
	  	foreach($_soin->_ref_prises as $_prise){
	  	  if($_prise->nb_tous_les && $_prise->unite_tous_les){
	  		  if($_prise->calculDatesPrise($date)){  	
	  		  	$prises_soin[$_soin->_id][] = $_prise;
	  		  } else {
	  		  	// liste des medicaments contenant des prises non prescrites pour la journée courante 
	  		  	$soinsNonPresc[$_soin->_id] = $_soin; 
	  		  }
	      } else {
	      	$prises_soin[$_soin->_id][] = $_prise;
	      }
	  	}
	  	$lines_soin[$_soin->_id] = $_soin;
	  }  
	}
}


foreach($medsNonPresc as $_line){
	// Si le medicament ne possede pas de prises
	if(!array_key_exists($_line->_id, $prises)){
		unset($lines_med[$_line->_id]);
	}
}
/*
foreach($soinsNonPresc as $_line_soin){
	// Si le medicament ne possede pas de prises
	if(!array_key_exists($_line_soin->_id, $prises_soin)){
		unset($lines_soin[$_line_soin->_id]);
	}
}
*/
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("date", $date);
$smarty->assign("prises", $prises);
$smarty->assign("prises_soin", $prises_soin);
$smarty->assign("lines_med", $lines_med);
$smarty->assign("lines_soin", $lines_soin);

$smarty->display("inc_vw_dossier_soins.tpl");

?>