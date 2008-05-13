<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/


//Initialisation des variables
$medicaments = array();
$prises = array();
$medocs = array();

// Ligne de titre
function createLineHoursTitle($pdf, $without_hour = 0, $date = ""){
	$tabHours = array("8h","12h","14h","18h","22h","24h","2h","6h");
	foreach($tabHours as $hour){
    $pdf->Cell(7,7,$hour,1,0,'C',1);  
	}
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(2,7,"",1,0,'C',1);	
	$pdf->SetFillColor(220,220,220);
}

// Ligne contenant les heures
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

function barre($pdf){
	$pdf->SetFillColor(220,220,220);
  $pdf->Cell(2,7,"",1,0,'C',1);
  $pdf->SetFillColor(255,255,255);
}

// Ligne de prescription
function prescriptionLine($pdf, $line, $date, $dates){	
	
	$line->loadRefsPrises();
	$line->loadRefPraticien();
	
	$view_prise = "";
	foreach($line->_ref_prises as $prise){
		$view_prise .= $prise->_view.", ";
	}

	$y_before = $pdf->getY();

	$signature = "";

	$pdf->MultiCell(60,3.5,utf8_encode($line->_view."\n ".$view_prise),"1","L");
	
	$y_after = $pdf->getY();
	$y = $y_after - $y_before;
	
	$pdf->setXY(75, $y_before);

	$valide = " ";
	// Prescripteur de la ligne
  if($line->_traitement){
  	$pdf->MultiCell(25,$y/2 ,"Traitement\npersonnel",1,'C',0,0);

    $valide = "-";
  } else {
	  $pdf->MultiCell(25,$y/2 ,utf8_encode($line->_ref_praticien->_user_first_name."\n".$line->_ref_praticien->_user_last_name),1,'C',0,0);
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
$date = mbDateTime();
//$date = "2008-04-28 07:49:46";

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadIPP();
$prescription->loadRefObject();
$sejour =& $prescription->_ref_object;
$sejour->loadNumDossier();

$consult_anesth = new CConsultAnesth();
$consult_anesth->sejour_id = $prescription->_ref_object->_id;
$consult_anesth->loadMatchingObject();

if($consult_anesth->_id){
  $poids = $consult_anesth->poid;
}

// Creation d'un nouveau fichier pdf
$pdf = new CPrescriptionPdf("L", "mm", "A4", true); 
$pdf->SetMargins(15, 15);
$pdf->setHeaderFont(Array("vera", '', "10"));
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);


$pdf->SetFont('','',"8");
$pdf->MultiCell(50,4,"IPP: ".$patient->_IPP."\n".utf8_encode($patient->_view),0,'C',0,0);

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
$pdf->MultiCell(40,4,"Age: ".$prescription->_ref_patient->_age." ans \nPoids: ".$poids." kg",0,'C',0,0);



$dateFormat = "%d/%m/%Y à %Hh%m";


$prescription->_ref_object->loadCurrentAffectation($date);

$pdf->SetFont('','',"8");

// Affichage de la chambre
if($prescription->_ref_object->_ref_curr_affectation->_id){
  $pdf->Cell(58,7,utf8_encode($prescription->_ref_object->_ref_curr_affectation->_ref_lit->_ref_chambre->_view),0,0,'C',1);
}
// Affichage du début du séjour
$pdf->Cell(58,7,utf8_encode("Début du séjour ".mbTranformTime(null, $prescription->_ref_object->_entree, $dateFormat)),0,0,'C',1);

// Affichage de la date et l'heure d'édition de la feuille de soin
$pdf->Cell(58,7,utf8_encode("Feuille de soin du ".mbTranformTime(null, $date, $dateFormat)),0,0,'C',1);


// Chargement de l'affectation courante du sejour
//$prescription->_ref_object->loadCurrentAffectation($date);
/*
if($prescription->_ref_object->_ref_curr_affectation->_id){
	$pdf->Cell(58,7,utf8_encode($prescription->_ref_object->_ref_curr_affectation->_ref_lit->_view),0,0,'C',1);
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
*/

$dateFormat = "%d/%m/%Y";
$pdf->setY(35);    

// Title du tableau
$pdf->SetFillColor(220,220,220);
$pdf->Cell(60,14,"Prescription",1,0,'C',1);
$pdf->Cell(25,14,"Prescripteur",1,0,'C',1);
$pdf->Cell(5, 14, "", 1,0, 'C',1);
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
$pdf->setY(49);
$pdf->SetFillColor(255,255,255);	

// Initialisation de last_log
$logs = "";

// Chargement des lignes de prescriptions
$prescription->loadRefsLines();

// Chargement des lignes de soins
$prescription->loadRefsLinesElement();

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
	calculEtatJour($date, &$dates, &$nb_jours, $line);
  if(!$nb_jours){
  	continue;
  }
  prescriptionLine($pdf, $line, $date, $dates);
  // CHargement et stockage des logs de validation pharmacien
  $line->loadRefLogValidationPharma();
  $logs[$line->_ref_log_validation_pharma->date] = $line->_ref_log_validation_pharma;
}


// Parcours des elements
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
$format_hour = "%Hh%m";


$pdf->Cell(60,21,"",1,0,'C',1);
$pdf->MultiCell(30,5.25,utf8_encode("Par ".utf8_encode($pharmacien->_user_first_name."\n".$pharmacien->_user_last_name)."\n le ".mbTranformTime(null, $last_log->date, $format_date)."\n à ".mbTranformTime(null, $last_log->date, $format_hour)),1,'C',0,0);

$pdf->Cell(56,7,"",1,0,'C',1);

signatureIDE($pdf);

$pdf->Output("plan_soin.pdf","I");

?>