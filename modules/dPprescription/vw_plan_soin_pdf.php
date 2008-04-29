<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/


// Ligne de titre
function createLineHoursTitle($pdf, $without_hour = 0, $date = ""){
	$tabHours = array("6h","8h","12h","14h","18h","22h","24h","2h");
	foreach($tabHours as $hour){
    $pdf->Cell(7,7,$hour,1,0,'C',1);  
	}
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(2,7,"",1,0,'C',1);	
	$pdf->SetFillColor(220,220,220);
}

// Ligne contenant les heures
function createLineHours($pdf, $y, $etat){
	$tabHours = array("6h","8h","12h","14h","18h","22h","24h","2h");
	if($etat == 0){
	  $pdf->SetFillColor(170,170,170);	
	}
	foreach($tabHours as $hour){
			$pdf->Cell(7,$y,"",1,0,'C',1);	 
	}
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(2,$y,"",1,0,'C',1);	
	$pdf->SetFillColor(255,255,255);	
}


// Ligne de prescription
//function prescriptionLine($pdf, $line_med, $line_id, $date){
function prescriptionLine($pdf, $line, $date, $dates){	
	
	$line->loadRefsPrises();
	$line->loadRefPraticien();
	
	$view_prise = "";
	foreach($line->_ref_prises as $prise){
		$view_prise .= $prise->_view.", ";
	}

	$y_before = $pdf->getY();

	$signature = "";
	if($line->_class_name == "CPrescriptionLineElement" && !$line->signee){
	  $signature = "(Non signé par le praticien)";
	}
	$pdf->MultiCell(50,4,utf8_encode($line->_view."\n ".$view_prise.$signature),"1","L");
	
	$y_after = $pdf->getY();
	$y = $y_after - $y_before;
	
	$pdf->setXY(65, $y_before);

	// Prescripteur de la ligne
  if($line->_traitement){
    $pdf->Cell(40,$y,"Traitement personnel",1,0,'C',1);	
  } else {
    $pdf->Cell(40,$y,$line->_ref_praticien->_view,1,0,'C',1);
  }
  /*
  $line_date_1 = array();
  $line_date_2 = array();
  $line_date_3 = array();
  
  if(array_key_exists($date, $line_med)){
  	$line_date_1 = $line_med[$date];
  }
  if(array_key_exists(mbDate("+ 1 day", $date), $line_med)){
  	$line_date_2 = $line_med[mbDate("+ 1 day", $date)];
  }
  if(array_key_exists(mbDate("+ 2 day", $date), $line_med)){
  	$line_date_3 = $line_med[mbDate("+ 2 day", $date)];
  }

  createLineHours($pdf, $line_date_1);
  createLineHours($pdf, $line_date_2);
  createLineHours($pdf, $line_date_3);
  */

  createLineHours($pdf, $y, $dates["jour_1"]);
  createLineHours($pdf, $y, $dates["jour_2"]);
  createLineHours($pdf ,$y, $dates["jour_3"]);
  $pdf->Ln();
}


function calculEtatJour($date, $dates, $nb_jours, $line){
  if(mbDate($date) >= $line->debut && mbDate($date) <= $line->_fin){
  	$dates["jour_1"] = 1;
  	$nb_jours++;
  }
  if(mbDate("+ 1 day",$date) >= $line->debut && mbDate("+ 1 day",$date) <= $line->_fin){
  	$dates["jour_2"] = 1;
    $nb_jours++;
  }
  if(mbDate("+ 2 day",$date) >= $line->debut && mbDate("+ 2 day",$date) <= $line->_fin){
  	$dates["jour_3"] = 1;
    $nb_jours++;
  }

}

$prises = array();
$lines_med = array();
$medsNonPresc = array();
$poids = "";
$logs = array();
$pharmacien = new CMediusers();
$last_log = new CUserLog();
$prescription_id = mbGetValueFromGet("prescription_id");
//$date = mbGetValueFromGet("date");
$date = mbDateTime();
//$date = "2008-04-28 07:49:46";


// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefPatient();
$prescription->loadRefObject();

$consult_anesth = new CConsultAnesth();
$consult_anesth->sejour_id = $prescription->_ref_object->_id;
$consult_anesth->loadMatchingObject();

if($consult_anesth->_id){
  $poids = $consult_anesth->poid;
}

/*
function addMedToArray(&$medicaments, $date, $lines){
	// Parcours de tous les medicaments de la prescription
	foreach($lines as &$line){
		if($line->date_arret && $line->date_arret < $date){
			continue;
		}
		if(!$line->valide_pharma){
			continue;
		}
	  if($date >= $line->debut && $date <= $line->_fin){
	  	$line->loadRefsPrises();  	
	  	foreach($line->_ref_prises as $prise){
	  		// Si nb_tous_les
	  	  if($prise->nb_tous_les && $prise->unite_tous_les){
	  		  if($prise->calculDatesPrise($date)){  	
	  		  	$medicaments[$line->_id][$date][] = $prise;
	  		  }
	      } else {
	      	$medicaments[$line->_id][$date][] = $prise;
	      }
	  	}
	  }   
	}


//foreach($medsNonPresc as $_line){
	// Si le medicament ne possede pas de prises
	//if(!array_key_exists($_line->_id, $prises)){
	//	unset($lines_med[$_line->_id]);
	//}
//}
}

*/



// Stockage du plan de soin dans un tableau
$medicaments = array();
$prises = array();
/*
addMedToArray($medicaments, $date, $lines);
addMedToArray($medicaments, mbDate("+ 1 day", $date) ,$lines);
addMedToArray($medicaments, mbDate("+ 2 day", $date), $lines);
*/

$medocs = array();
// Parcours des medicaments 
/*
foreach($medicaments as $medicament_id => $jours){
	foreach($jours as $jour => $prises){
		$liste_prise = array("6" => "","8" => "","12" => "","14" => "","18" => "","22" => "","24" => "","2" => "","libre" => array());
	    
		foreach($prises as $prise){
			$libre = array();
			if($prise->nb_tous_les){
	    	mbTrace($prise->nb_tous_les);
	    	$liste_prise["libre"][] = $prise->quantite." ".$prise->_ref_object->_unite_prise;	
	    }
		  if($prise->nb_fois){
	    	$liste_prise["libre"][] = $prise->_view;	
	    }
	    if(!$prise->nb_tous_les && !$prise->nb_fois && !$prise->moment_unitaire_id){
	     	$liste_prise["libre"][] = $prise->_view;	
	    }
	    
	    //if($prise->moment_unitaire_id){
	    //	$liste_prise[$prise->_ref_heure_moment] += $prise->quantite;
	    //}
		}
		
	$medocs[$medicament_id][$jour] = $liste_prise;
	}
}
*/

// Creation d'un nouveau fichier pdf
$pdf = new CPrescriptionPdf("L", "mm", "A4", true); 

// Définition des marges de la pages
$pdf->SetMargins(15, 20);

// Définition de la police et de la taille de l'entete
$pdf->setHeaderFont(Array("vera", '', "10"));

// Creation d'une nouvelle page
$pdf->AddPage();

$pdf->SetFillColor(255,255,255);

$pdf->SetFont('','',"16");
$pdf->Cell(50,7,utf8_encode($prescription->_ref_patient->_view),0,0,'C',1);
/*
$pdf->SetFont('','',"10");
$pdf->Cell(40,5,"Age: ".$prescription->_ref_patient->_age." ans",0,0,'C',1);
$pdf->SetFont('','',"11");
*/

$pdf->SetFont('','',"10");
$pdf->MultiCell(40,4,"Age: ".$prescription->_ref_patient->_age." ans \nPoids: ".$poids." kg",0,'C',0,0);

  
$dateFormat = "%d/%m/%Y à %Hh%m";

// Chargement de l'affectation courante du sejour
$prescription->_ref_object->loadCurrentAffectation($date);

if($prescription->_ref_object->_ref_curr_affectation->_id){
	$pdf->Cell(58,7,$prescription->_ref_object->_ref_curr_affectation->_ref_lit->_view,0,0,'C',1);
	$pdf->SetFont('','',"10");
}

$pdf->SetFont('','',"8");
if($prescription->_ref_object->_ref_before_affectation->_id){
  $pdf->MultiCell(58,4,utf8_encode("Depuis le ".mbTranformTime(null, $prescription->_ref_object->_ref_curr_affectation->entree, $dateFormat)." \n (Depuis ".$prescription->_ref_object->_ref_before_affectation->_ref_lit->_view.")"),0,'C',0,0);
} else {
  $pdf->Cell(58,7,utf8_encode("Depuis le ".mbTranformTime(null, $prescription->_ref_object->_ref_curr_affectation->entree, $dateFormat)),0,0,'C',1);
}

if($prescription->_ref_object->_ref_next_affectation->_id){
  $pdf->MultiCell(58,4,utf8_encode("Jusqu'au ".mbTranformTime(null, $prescription->_ref_object->_ref_curr_affectation->sortie, $dateFormat)." \n (Vers ".$prescription->_ref_object->_ref_next_affectation->_ref_lit->_view.")"),0,'C',0,0);
} else {
  $pdf->MultiCell(58,4,utf8_encode("Jusqu'au ".mbTranformTime(null, $prescription->_ref_object->_ref_curr_affectation->sortie, $dateFormat)." \n (Sortie)"),0,'C',0,0);
}
  
$pdf->Ln();
//$pdf->setX(65);
//$pdf->Cell(40,0,"Poids: ".$poids." kg",0,0,'C',1);
$pdf->Ln();
$pdf->Ln();

$dateFormat = "%d/%m/%Y";
$pdf->setY(34);    
// Title du tableau
$pdf->SetFillColor(220,220,220);
$pdf->Cell(50,14,"Prescription",1,0,'C',1);
$pdf->Cell(40,14,"Prescripteur",1,0,'C',1);
$pdf->Cell(56,7,mbTranformTime(null, $date, $dateFormat),1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,mbTranformTime(null, mbDate("+ 1 day", $date), $dateFormat),1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,mbTranformTime(null, mbDate("+ 2 day", $date), $dateFormat),1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);

$pdf->Ln();

$pdf->setX(105);
$pdf->SetFont('','',"8");

createLineHoursTitle($pdf);
createLineHoursTitle($pdf);
createLineHoursTitle($pdf);

$pdf->setX(15);
$pdf->setY(48);
$pdf->SetFillColor(255,255,255);	

// Initialisation de last_log
$logs = "";

// Chargement des lignes de prescriptions
$prescription->loadRefsLines();

// Chargement des lignes de soins
$prescription->loadRefsLinesElement("soin");

// Chargement des traitements
$prescription->_ref_object->loadRefPrescriptionTraitement();
$traitement = $prescription->_ref_object->_ref_prescription_traitement;
if($traitement->_id){
  $traitement->loadRefsLines();
	
	// Parcours des traitements
	foreach($traitement->_ref_prescription_lines as $_line_traitement){
	  $dates = array("jour_1" => "0","jour_2" => "0","jour_3" => "0");
	  $nb_jours = 0;
	  
	  $_line_traitement->debut = mbDate($prescription->_ref_object->_entree);
	  $_line_traitement->_fin = mbDate($prescription->_ref_object->_sortie);
	  
		if($_line_traitement->date_arret){
	    $_line_traitement->_fin = $_line_traitement->date_arret;
	  }
	  $_line_traitement->debut = mbDate($prescription->_ref_object->_entree);
	  
	  calculEtatJour($date, &$dates, &$nb_jours, $_line_traitement);
	  
	  if(!$nb_jours){
	  	continue;
	  }
	  prescriptionLine($pdf, $_line_traitement, $date, $dates);
	}
}
// Parcours des medicaments
foreach($prescription->_ref_prescription_lines as $line){
  $dates = array("jour_1" => "0","jour_2" => "0","jour_3" => "0");
  $nb_jours = 0;
  
	if($line->date_arret){
    $line->_fin = $line->date_arret;
  }
  
  // On arrete dans tous les cas la feuille de soin a la fin du sejour
  if($line->_fin > $prescription->_ref_object->_sortie){
  	$line->_fin = mbDate($prescription->_ref_object->_sortie);
  }
  if($line->debut < $prescription->_ref_object->_entree){
  	$line->debut = mbDate($prescription->_ref_object->_entree);
  }

	if(!$line->valide_pharma){
		continue;
	}
	calculEtatJour($date, &$dates, &$nb_jours, $line);
  
  if(!$nb_jours){
  	continue;
  }
  prescriptionLine($pdf, $line, $date, $dates);

  // CHargement et stockage des logs de validation pharmacien
  $line->loadRefLogValidationPharma();
  $logs[$line->_ref_log_validation_pharma->date] = $line->_ref_log_validation_pharma;
}

// Parcours des soins
foreach($prescription->_ref_prescription_lines_element as $_soin){
  $dates = array("jour_1" => "0","jour_2" => "0","jour_3" => "0");
  $nb_jours = 0;
  
	if($_soin->date_arret){
    $_soin->_fin = $_soin->date_arret;
  }
  // On arrete dans tous les cas la feuille de soin a la fin du sejour
  if($_soin->_fin > $prescription->_ref_object->_sortie){
  	$_soin->_fin = mbDate($prescription->_ref_object->_sortie);
  }
  if($_soin->debut < $prescription->_ref_object->_entree){
  	$_soin->debut = mbDate($prescription->_ref_object->_entree);
  }
  calculEtatJour($date, &$dates, &$nb_jours, $_soin);
  
  if(!$nb_jours){
  	continue;
  }
  prescriptionLine($pdf, $_soin, $date, $dates);
}

// Chargement du dernier pharmacien qui a validé une ligne
if($logs){
  ksort($logs);
  $last_log = end($logs);
  $pharmacien->load($last_log->user_id);
}

// Separateur
$pdf->SetFillColor(220,220,220);
$pdf->Cell(264,2,"",1,0,'C',1);
$pdf->Ln();

// Affichage des signatures
$pdf->Cell(50,7,"Remarques",1,0,'C',1);
$pdf->Cell(40,7,"Validation Pharmacien",1,0,'C',1);
$pdf->Cell(56,7,"Signature IDE",1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,"Signature IDE",1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,"Signature IDE",1,0,'C',1);

$pdf->SetFillColor(220,220,220);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->SetFillColor(255,255,255);	

$pdf->Ln();

$dateFormat = "%d/%m/%Y à %Hh%m";

$pdf->Cell(50,20,"",1,0,'C',1);
$pdf->MultiCell(40,10,utf8_encode("Par ".$pharmacien->_view."\n le ".mbTranformTime(null, $last_log->date, $dateFormat)),1,'C',0,0);
$pdf->Cell(56,20,"",1,0,'C',1);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(2,20,"",1,0,'C',1);
$pdf->SetFillColor(255,255,255);	
$pdf->Cell(56,20,"",1,0,'C',1);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(2,20,"",1,0,'C',1);
$pdf->SetFillColor(255,255,255);	
$pdf->Cell(56,20,"",1,0,'C',1);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(2,20,"",1,0,'C',1);

$pdf->Output("plan_soin.pdf","I");

?>