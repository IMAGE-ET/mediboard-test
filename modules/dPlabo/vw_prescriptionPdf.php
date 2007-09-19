<?php

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Alexis Granger
 */


// Recuperation de l'id de la prescription
$prescription_id = mbGetValueFromGet("prescription_id");

// Chargement de la prescription selectionnée
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();
$prescription->_ref_praticien->loadRefFunction();
$prescription->_ref_praticien->_ref_function->loadRefsFwd();
$prescription->loadRefsBack();


// Creation d'un nouveau fichier pdf
$pdf = new CPrescriptionPdf("P", "mm", "A4", true); 

// Affichage de l'entete du document
$image = "logo.jpg";

$taille = "75";
$texte = "Av. des Sciences 1B\nCase postale 961\n1401 Yverdon-les-Bains\nTel:  024 424 80 50\nFax: 024 424 80 51";
$pdf->SetHeaderData($image, $taille, "", $texte);


// Définition des marges de la pages
$pdf->SetMargins(15, 27, 15);
$pdf->initMarge("5","10");

// Définition de la police et de la taille de l'entete
$pdf->setHeaderFont(Array("vera", '', "10"));

// Creation d'une nouvelle page
$pdf->AddPage();

$praticien =& $prescription->_ref_praticien;
$patient =& $prescription->_ref_patient;

// Affichage du praticien et du patient à l'aide d'un tableau
$pdf->createTab($pdf->viewPraticien($praticien->_view,$praticien->_ref_function->_view, $praticien->_ref_function->_ref_group->_view),
                $pdf->viewPatient($patient->_view, $patient->adresse, $patient->cp, $patient->ville, $patient->tel));
                
//Saut de ligne
$pdf->Ln(30);

// Affichage des analyses
$pdf->Cell(180,7,utf8_encode("Analyses demandées:"),0);
$pdf->Ln(8);
	
$pdf->SetFillColor(246,246,246);
$pdf->Cell(25,7,utf8_encode("Identifiant"),1,0,'C',1);
$pdf->Cell(125,7,utf8_encode("Libellé de l'analyse"),1,0,'C',1);
$pdf->Cell(30,7,utf8_encode("Type"),1,0,'C',1);
$pdf->Ln();
    

foreach($prescription->_ref_prescription_items as $key => $prescription){
    $examen_labo =& $prescription->_ref_examen_labo;
	//$pdf->SetFillColor(230,245,255);
	$pdf->Cell(25,7,utf8_encode($examen_labo->identifiant),1,0,'L',0);
    $pdf->Cell(125,7,utf8_encode($examen_labo->libelle),1,0,'L',0);
	$pdf->Cell(30,7,utf8_encode($examen_labo->type_prelevement),1,0,'L',0);
    $pdf->Ln();
}

// Initialisation du code barre, => utilisation par default du codage C128B
// L'affichage du code barre est realisee dans la fonction redefinie Footer dans la classe CPrescriptionPdf
$pdf->SetBarcode($prescription->_id);

// Nom du fichier: prescription.pdf   / I : sortie standard
$pdf->Output("prescription.pdf","I");


?>