<?php

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $dPconfig;

// Recuperation de l'id de la prescription
$prescription_id = mbGetValueFromGet("prescription_id");

// Chargement de la prescription selectionnée
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();
$prescription->_ref_praticien->loadRefFunction();
$prescription->_ref_praticien->_ref_function->loadRefsFwd();
$prescription->loadRefsBack();

$tab_prescription = array();
$tab_pack_prescription = array();

// Creation d'un nouveau fichier pdf
$pdf = new CPrescriptionPdf("P", "mm", "A4", true); 



// Affichage de l'entete du document
$image = "logo.jpg";
$taille = "75";
$texte = "Av. des Sciences 1B\nCase postale 961\n1401 Yverdon-les-Bains\nTel:  024 424 80 50\nFax: 024 424 80 51";
$pdf->SetHeaderData($image, $taille, "", $texte);

// Définition des marges de la pages
$pdf->SetMargins(15, 40);

// Définition de la police et de la taille de l'entete
$pdf->setHeaderFont(Array("vera", '', "10"));

// Creation d'une nouvelle page
$pdf->AddPage();

$praticien =& $prescription->_ref_praticien;
$patient =& $prescription->_ref_patient;

// Affichage du praticien et du patient à l'aide d'un tableau
$pdf->createTab($pdf->viewPraticien($praticien->_view,$praticien->_ref_function->_view, $praticien->_ref_function->_ref_group->_view),
                $pdf->viewPatient($patient->_view, mbTranformTime($patient->naissance,null,'%d-%m-%y'), $patient->adresse, $patient->cp, $patient->ville, $patient->tel));

$urgent = "";
if($prescription->urgence){
	$urgent = "(URGENT)";
}
$pdf->setY(65);
$pdf->writeHTML(utf8_encode("<b>Prélèvement du ".(mbTranformTime($prescription->date,null,'%d-%m-%y à %H:%M'))." ".$urgent."</b>"));

$pdf->setY(80);
// Affichage des analyses
$pdf->writeHTML(utf8_encode("<b>Analyses demandées:</b>"));
	
$pdf->SetFillColor(246,246,246);
$pdf->Cell(25,7,utf8_encode("Identifiant"),1,0,'C',1);
$pdf->Cell(125,7,utf8_encode("Libellé de l'analyse"),1,0,'C',1);
$pdf->Cell(30,7,utf8_encode("Type"),1,0,'C',1);
$pdf->Ln();



$tagCatalogue = $dPconfig['dPlabo']['CCatalogueLabo']['remote_name'];

// Chargement de l'id externe labo code4 du praticien
// Chargement de l'id400 "labo code4" du praticien
$tagCode4 = "labo code4";
$idSantePratCode4 = new CIdSante400();
$idSantePratCode4->loadLatestFor($praticien, $tagCode4);


if($idSantePratCode4->id400){
	$numPrat = $idSantePratCode4->id400;
	$numPrat = str_pad($numPrat, 4, '0', STR_PAD_LEFT);
} else {
	$numPrat = "xxxx";
}

// Chargement de la valeur de l'id externe de la prescription ==> retourne uniquement l'id400
if($prescription->verouillee){
  $id400Presc = $prescription->loadIdPresc();
  $id400Presc = str_pad($id400Presc, 4, '0', STR_PAD_LEFT);
} else {
  $id400Presc = "xxxx";
}

$num = $numPrat.$id400Presc;


// Initialisation du code barre, => utilisation par default du codage C128B
// L'affichage du code barre est realisee dans la fonction redefinie Footer dans la classe CPrescriptionPdf
$pdf->SetBarcode($num, $prescription->_ref_praticien->_user_last_name, $prescription->_ref_patient->_view, $prescription->_ref_patient->sexe,mbTranformTime($prescription->_ref_patient->naissance,null,"%d-%m-%y"), mbTranformTime($prescription->date,null,"%d-%m-%y %H:%M"));



// Tableau de classement des analyses par pack
foreach($prescription->_ref_prescription_items as $key => $item){
  if($item->_ref_pack->_id){
    $tab_pack_prescription[$item->_ref_pack->_view][] = $item;  
  }
  else {
    $tab_prescription[] = $item;    
  }
}


foreach($tab_pack_prescription as $key => $pack){
  if($key){
    $pdf->Cell(0,7,utf8_encode($key),1,0,'C',1);
    $pdf->Ln();
  }
  foreach($pack as $key2 => $_item){
    $examen_labo =& $_item->_ref_examen_labo;
  	//$pdf->SetFillColor(230,245,255);
	  $pdf->Cell(25,7,utf8_encode($examen_labo->identifiant),1,0,'L',0);
    $pdf->Cell(125,7,utf8_encode($examen_labo->libelle),1,0,'L',0);
	  $pdf->Cell(30,7,utf8_encode($examen_labo->type_prelevement),1,0,'L',0);
    $pdf->Ln();
    
    // si on atteint y max de contenu de la page, on change de page
    if($pdf->getY() > 200){
      $pdf->AddPage();
    }
  }
}

if($tab_pack_prescription && $tab_prescription){
  $pdf->Cell(0,7,"Autres analyses",1,0,'C',1);
  $pdf->Ln();
}
  
foreach($tab_prescription as $key => $_item){
  $examen_labo =& $_item->_ref_examen_labo;
  //$pdf->SetFillColor(230,245,255);
	$pdf->Cell(25,7,utf8_encode($examen_labo->identifiant),1,0,'L',0);
  $pdf->Cell(125,7,utf8_encode($examen_labo->libelle),1,0,'L',0);
	$pdf->Cell(30,7,utf8_encode($examen_labo->type_prelevement),1,0,'L',0);
  $pdf->Ln();
  if($pdf->getY() > 200){
    $pdf->AddPage();
  }
}

// Nom du fichier: prescription-xxxxxxxx.pdf   / I : sortie standard
$pdf->Output("prescription-$num.pdf","I");

?>