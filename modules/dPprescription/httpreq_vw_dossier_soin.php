<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$date = mbGetValueFromGetOrSession("date");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefPraticien();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->loadMatchingObject();

$prescription_id = $prescription->_id;

$prises_med = array();
$prises_element = array();
$lines_med = array();
$lines_element = array();
$medsNonPresc = array();
$elementsNonPresc = array();


// Chargement des categories
$categorie = new CCategoryPrescription();
$cats = $categorie->loadList();
foreach($cats as $key => $cat){
	$categories["cat".$key] = $cat;
}

$lines_med_trait = array();

if($prescription->_id){
	// Chargement des lignes de medicaments
  $prescription->loadRefsLines();
  // Chargement des lignes d'elements
  $prescription->loadRefsLinesElementByCat();
  // Chargement des lignes de traitement
  $prescription->_ref_object->loadRefPrescriptionTraitement();
  
  $lines_med_trait["medicament"] = $prescription->_ref_prescription_lines;
  $traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
  if($traitement_personnel->_id){
    $traitement_personnel->loadRefsLines();
  }
  $lines_med_trait["traitement"] = $traitement_personnel->_ref_prescription_lines;
  
  // Parcours des medicaments
  foreach($lines_med_trait as $cat_name => $lines_cat){
  	if($lines_cat|@count){
		foreach($lines_cat as &$line){
			$view_line = 0;
			if($line->date_arret){
				$line->_fin = mbDate("- 1 DAY", $line->date_arret);
			}
			if($line->_fin && $line->_fin < $date){
				continue;
			}
			
			// Si pas de date de fin, on affiche tout le tps la ligne dans le dossier de soin
			if(!$line->_fin && $line->debut <= $date){
				$view_line = 1;
			}

		  if(($date >= $line->debut && $date <= $line->_fin) || $view_line){
		  	$line->loadRefsPrises();  	
		  	foreach($line->_ref_prises as $prise){
		  	  if($prise->nb_tous_les && $prise->unite_tous_les){
		  		  if($prise->calculDatesPrise($date)){  	
		  		  	$prises_med[$line->_id][] = $prise;
		  		  } else {
		  		  	// liste des medicaments contenant des prises non prescrites pour la journée courante 
		  		  	$medsNonPresc[$line->_id] = $line; 
		  		  }
		      } else {
		      	$prises_med[$line->_id][] = $prise;
		      }
		  	}
		  	$lines_med[$line->_id] = $line;
		  }  
		}
  	}
  }
  foreach($prescription->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
  	if($name_chap != "dmi"){
	  	foreach($elements_chap as $name_cat => $elements_cat){
	  		foreach($elements_cat as $_elements){
	  			foreach($_elements as $_element){
	  				$view_line_element = 0;
	  				// Si les dates ne correspondent pas, on passe au suivant
            if($_element->date_arret){
            	$_element->_fin = mbDate("- 1 DAY", $_element->date_arret);
            }
	  				if($_element->_fin && $_element->_fin < $date){
			        continue;
		        }
		        if(!$_element->_fin && $_element->debut == $date){
		        	$view_line_element = 1;
		        }
		        if(($date >= $_element->debut && $date <= $_element->_fin) || $view_line_element){
					  	// Si l'element est un DM, on le rajoute dans la liste
		        	if($name_chap == "dm"){
					  		$lines_element[$name_chap][$name_cat][$_element->_id] = $_element;
					  	} 
					  	// Sinon, on regarde si l'element possède des prises pour la date donnée
					  	else {
			        	// Chargement des prises
		  			  	$_element->loadRefsPrises();  	
						  	foreach($_element->_ref_prises as $_prise){
						  	  if($_prise->nb_tous_les && $_prise->unite_tous_les){
						  		  if($_prise->calculDatesPrise($date)){  	
						  		  	$prises_element[$_element->_id][] = $_prise;
						  		  } else {
						  		  	// liste des medicaments contenant des prises non prescrites pour la journée courante 
						  		  	$elementsNonPresc[$_element->_id] = $_element; 
						  		  }
						      } else {
						      	$prises_element[$_element->_id][] = $_prise;
						      }
						  	}
						  	//if(array_key_exists($_element->_id, $prises_element)){
						      $lines_element[$name_chap][$name_cat][$_element->_id] = $_element;
						  	//}		
		  			  }
		        }
	    	  }
	  	  }
  	  }
    }
  }
}


foreach($medsNonPresc as $_line){
	// Si le medicament ne possede pas de prises
	if(!array_key_exists($_line->_id, $prises_med)){
		unset($lines_med[$_line->_id]);
	}
}



// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("date", $date);
$smarty->assign("prises_med", $prises_med);
$smarty->assign("lines_med", $lines_med);
$smarty->assign("prises_element",$prises_element);
$smarty->assign("lines_element", $lines_element);
$smarty->assign("categories", $categories);
$smarty->display("inc_vw_dossier_soins.tpl");

?>