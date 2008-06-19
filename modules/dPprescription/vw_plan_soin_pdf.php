<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

/*
 * Affichage des lignes contenant les titres
 */
function createLineHoursTitle($pdf, $without_hour = 0, $date = ""){
	$tabHours = array("8h","12h","14h","18h","22h","24h","2h","6h");
	foreach($tabHours as $hour){
    $pdf->Cell(7,7,$hour,1,0,'C',1);  
	}
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(2,7,"",1,0,'C',1);	
	$pdf->SetFillColor(220,220,220);
}

/*
 * Affichage des lignes contenant les heures
 */
function createLineHours($pdf, $y, $etat){
	$tabHours = array("8h","12h","14h","18h","22h","24h","2h","6h");
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


/*
 * Affichage des signatures infirmieres
 */
function signatureIDE($pdf){
	for($i=0;  $i<3; $i++) {
	  $pdf->setX(105);
	  for($j=0;  $j<3; $j++) {
      $pdf->Cell(56,7,"",1,0,'C',1);
	    barre($pdf);
	  }
		$pdf->Ln();
	}
}

/*
 * Affichage d'une barre de separation
 */
function barre($pdf){
	$pdf->SetFillColor(220,220,220);
  $pdf->Cell(2,7,"",1,0,'C',1);
  $pdf->SetFillColor(255,255,255);
}


/*
 * Affichage d'une ligne de prescription
 */
function prescriptionLine($pdf, $line, $date, $dates){	
	$line->loadRefsPrises();
	$line->loadRefPraticien();
	
	$view_prise = "";
	foreach($line->_ref_prises as $prise){
		$view_prise .= $prise->_view.", ";
	}
	$y_before = $pdf->getY();
	$signature = "";
	$pdf->MultiCell(60,3.5,$line->_view."\n ".$view_prise,"1","L");
	$y_after = $pdf->getY();
	$y = $y_after - $y_before;
	$pdf->setXY(75, $y_before);
	$valide = " ";

	// Affichage du prescripteur de la ligne
  if($line->_traitement){
  	$pdf->MultiCell(25,$y/2 ,"Traitement\npersonnel",1,'C',0,0);
    $valide = "-";
  } else {
	  $pdf->MultiCell(25,$y/2 ,$line->_ref_praticien->_user_first_name."\n".$line->_ref_praticien->_user_last_name,1,'C',0,0);
    if(!$line->signee){
    	$valide = "D";
    }
    if($line->_class_name == "CPrescriptionLineMedicament" && !$line->valide_pharma){
    	$valide .= "P";
    }
  }
  $pdf->Cell(5,$y,$valide,1,0,'C',1);
  createLineHours($pdf, $y, $dates["jour_1"]);
  createLineHours($pdf, $y, $dates["jour_2"]);
  createLineHours($pdf ,$y, $dates["jour_3"]);
  $pdf->Ln();
}

/*
 * Fonction permettant de savoir si une case est grisée ou non
 */
function calculEtatJour($date, &$dates, &$nb_jours, $line){
  $tab_dates = array("jour_1" => mbDate($date), "jour_2" => mbDate("+ 1 day",$date),  "jour_3" => mbDate("+ 2 day",$date));
  $dates = array("jour_1" => "0", "jour_2" => "0", "jour_3" => "0");
  $nb_jours = 0;
  foreach($tab_dates as $key => $_date){
	  if(($_date >= $line->debut && $_date <= $line->_date_arret_fin) || (!$line->_date_arret_fin && $line->debut <= $_date)){
	  	$dates[$key] = 1;
	  	$nb_jours++;
	  }
  }
}

//Initialisation des variables
$medicaments     = array();
$prises          = array();
$medocs          = array();
$lines_med       = array();
$medsNonPresc    = array();
$poids           = "";
$logs            = array();
$pharmacien      = new CMediusers();
$last_log        = new CUserLog();
$prescription_id = mbGetValueFromGet("prescription_id");
$date            = mbDate();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement des lignes de prescriptions
$prescription->loadRefsLines("1");
$prescription->loadRefsLinesElement();

// Chargement de la prescription de traitement
$prescription->_ref_object->loadRefPrescriptionTraitement();
$prescription_traitement = $prescription->_ref_object->_ref_prescription_traitement;

// Chargement du patient
$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadIPP();
$patient->loadRefConstantesMedicales();
$const_med = $patient->_ref_constantes_medicales;
$poids = $const_med->poids;

// Chargement du séjour
$prescription->loadRefObject();
$sejour =& $prescription->_ref_object;
$sejour->loadNumDossier();
$sejour->loadCurrentAffectation(mbDateTime());


// Creation d'un nouveau fichier pdf
$pdf = new CPrescriptionPdf("L", "mm", "A4", true); 
$pdf->SetMargins(15, 15);
$pdf->setHeaderFont(Array("vera", '', "10"));
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('','',"8");

// Affichage du patient
$pdf->MultiCell(50,4,"IPP: ".$patient->_IPP."\n".$patient->_view,0,'C',0,0);

// Affichage d'un numéro de dossier
if($sejour->_num_dossier && $sejour->_num_dossier != "-"){
  $pdf->setBarcode($sejour->_num_dossier);
  $pdf->viewBarcodeSoin(25,23,4);
	$pdf->SetFont('','',"7");
	$pdf->Ln();
	$pdf->setXY(20, 28);
	$pdf->MultiCell(40,4,"Dossier ".$sejour->_num_dossier,0,'C',0,0);
}

$pdf->setXY(65, 15);
$pdf->SetFont('','',"10");

// Affichage du poids et de l'age du patient
$pdf->MultiCell(40,4,"Age: ".$prescription->_ref_patient->_age." ans \nPoids: ".$poids." kg",0,'C',0,0);

$dateFormat = "%d/%m/%Y à %Hh%m";
$pdf->SetFont('','',"8");

// Affichage de la chambre
if($prescription->_ref_object->_ref_curr_affectation->_id){
  $pdf->Cell(58,7,$prescription->_ref_object->_ref_curr_affectation->_ref_lit->_ref_chambre->_view,0,0,'C',1);
}
// Affichage du début du séjour
$pdf->Cell(58,7,"Début du séjour ".mbTransformTime(null, $prescription->_ref_object->_entree, $dateFormat),0,0,'C',1);

// Affichage de la date et l'heure d'édition de la feuille de soin
$pdf->Cell(58,7,"Feuille de soin du ".mbTransformTime(null, $date, $dateFormat),0,0,'C',1);

$dateFormat = "%d/%m/%Y";
$pdf->setY(35);    


// Affichage de l'entete du tableau
$pdf->SetFillColor(220,220,220);
$pdf->Cell(60,14,"Prescription",1,0,'C',1);
$pdf->Cell(25,14,"Prescripteur",1,0,'C',1);
$pdf->Cell(5, 14, "", 1,0, 'C',1);
$pdf->Cell(56,7,mbTransformTime(null, $date, $dateFormat),1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,mbTransformTime(null, mbDate("+ 1 day", $date), $dateFormat),1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,mbTransformTime(null, mbDate("+ 2 day", $date), $dateFormat),1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Ln();
$pdf->setX(105);
$pdf->SetFont('','',"8");
createLineHoursTitle($pdf);
createLineHoursTitle($pdf);
createLineHoursTitle($pdf);
$pdf->setX(15);
$pdf->setY(49);
$pdf->SetFillColor(255,255,255);	

// Parcours et affichage des traitements
if($prescription_traitement->_id){
  $prescription_traitement->loadRefsLines();
	// Parcours des traitements
	foreach($prescription_traitement->_ref_prescription_lines as $_line_traitement){
		// On arrete dans tous les cas la feuille de soin a la fin du sejour
	  if($line->_fin >= $prescription->_ref_object->_sortie){
    	$line->_fin = mbDate($prescription->_ref_object->_sortie);
    }
    if($line->debut <= $prescription->_ref_object->_entree){
    	$line->debut = mbDate($prescription->_ref_object->_entree);
    }
		
	  calculEtatJour($date, $dates, $nb_jours, $_line_traitement);
	  prescriptionLine($pdf, $_line_traitement, $date, $dates);
	}
}

// Parcours et affichage des medicaments
foreach($prescription->_ref_prescription_lines as $line){
	// On arrete dans tous les cas la feuille de soin a la fin du sejour
	if($line->_fin >= $prescription->_ref_object->_sortie){
  	$line->_fin = mbDate($prescription->_ref_object->_sortie);
  }
  if($line->debut <= $prescription->_ref_object->_entree){
  	$line->debut = mbDate($prescription->_ref_object->_entree);
  }
  calculEtatJour($date, $dates, $nb_jours, $line);
  prescriptionLine($pdf, $line, $date, $dates);
  
  // Chargement et stockage des logs de validation pharmacien
  $line->loadRefLogValidationPharma();
  $logs[$line->_ref_log_validation_pharma->date] = $line->_ref_log_validation_pharma;
}


// Parcours et affichage des elements
foreach($prescription->_ref_prescription_lines_element as $_elt){
  // On arrete dans tous les cas la feuille de soin a la fin du sejour
  if($_elt->_fin > $prescription->_ref_object->_sortie){
  	$_elt->_fin = mbDate($prescription->_ref_object->_sortie);
  }
  if($_elt->debut < $prescription->_ref_object->_entree){
  	$_elt->debut = mbDate($prescription->_ref_object->_entree);
  }
  calculEtatJour($date, $dates, $nb_jours, $_elt);
  prescriptionLine($pdf, $_elt, $date, $dates);
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
$pdf->Cell(60,7,"Remarques",1,0,'C',1);
$pdf->Cell(30,7,"Visa Pharmacien",1,0,'C',1);
$pdf->Cell(56,7,"Signature IDE",1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,"Signature IDE",1,0,'C',1);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->Cell(56,7,"Signature IDE",1,0,'C',1);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(2,7,"",1,0,'C',1);
$pdf->SetFillColor(255,255,255);	
$pdf->Ln();

$format_date = "%d/%m/%Y";
$format_hour = "%Hh%M";


// Affichage de la vaidation du pharmacien
$pdf->Cell(60,21,"",1,0,'C',1);
if($last_log->_id){
  $pdf->MultiCell(30,5.25,"Par ".$pharmacien->_user_first_name."\n".$pharmacien->_user_last_name."\n le ".mbTransformTime(null, $last_log->date, $format_date)."\n à ".mbTransformTime(null, $last_log->date, $format_hour),1,'C',0,0);
} else {
	$pdf->MultiCell(30,21,"",1,'C',0,0);
}
$pdf->Cell(56,7,"",1,0,'C',1);

signatureIDE($pdf);

$pdf->Output("plan_soin.pdf","I");

?>