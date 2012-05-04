<?php
$colonnes = array(20, 28, 25, 75, 30);
function ajoutEntete1($pdf, $facture, $user, $praticien, $group, $colonnes){
	ajoutEntete2($pdf, 1, $facture, $user, $praticien, $group, $colonnes);
  $pdf->SetFillColor(255, 255, 255);
  $pdf->SetDrawColor(0);
  $pdf->Rect(10, 38, 180, 100,'DF');
  
  $lignes = array(
    array("Patient", "Nom", $facture->_ref_patient->nom),
    array(""      , "Prénom", $facture->_ref_patient->prenom),
    array(""      , "Rue", $facture->_ref_patient->adresse),
    array(""      , "NPA", $facture->_ref_patient->cp),
    array(""      , "Localité", $facture->_ref_patient->ville),
    array(""      , "Date de naissance", mbTransformTime(null, $facture->_ref_patient->naissance, "%d.%m.%Y")),
    array(""      , "Sexe", $facture->_ref_patient->sexe),
    array(""      , "Date cas", mbTransformTime(null, $facture->cloture, "%d.%m.%Y")),
    array(""      , "N° cas", ""),
    array(""      , "N° AVS", $facture->_ref_patient->matricule),
    array(""      , "N° Cada", ""),
    array(""      , "N° assuré", ""),
    array(""      , "Canton", ""),
    array(""      , "Copie", "Non"),
    array(""      , "Type de remb.", "TG"),
    array(""      , "Loi", "LAMal"),
    array(""      , "N° contrat", ""),
    array(""      , "Traitement", "-"),
    array(""      , "N°/Nom entreprise"),
    array(""      , "Rôle/ Localité", mbTransformTime(null, $facture->ouverture, "%d.%m.%Y")." - ".mbTransformTime(null, $facture->cloture, "%d.%m.%Y")),
    array("Mandataire", "N° EAN/N° RCC", $praticien->ean." - ".$praticien->rcc." "),
    array("Diagnostic", "Contrat", "ICD--"),
    array("Liste EAN" , "", "1/".$praticien->ean." 2/".$user->ean),
    array("Commentaire")
  );
  $font = "vera";
  $pdf->setFont($font, '', 8);
  foreach($lignes as $ligne){
    $pdf->setXY(10, $pdf->getY()+4);
    foreach($ligne as $key => $value){
      $pdf->Cell($colonnes[$key], "", $value);
    }
  }
  $pdf->Line(10, 119, 190, 119);
  $pdf->Line(10, 123, 190, 123);
  $pdf->Line(10, 127, 190, 127);
  $pdf->Line(10, 131, 190, 131);
}
function ajoutEntete2($pdf, $nb, $facture, $user, $praticien, $group, $colonnes){
  $font = "verab";
  $pdf->setFont($font, '', 12);
  $pdf->WriteHTML("<h4>Justificatif de remboursement</h4>");
  $font = "vera";
  $pdf->setFont($font, '', 8);
  $pdf->SetFillColor(255, 255, 255);
  $pdf->SetDrawColor(0);
  $pdf->Rect(10, 18, 180,20,'DF');
  $lignes = array(
    array("Document", "Identification", $facture->_id." ".mbTransformTime(null, null, "%d.%m.%Y %H:%M:%S"), "", "Page $nb"),
    array("Auteur", "N° EAN(B)", "$user->ean", "$user->_view", " Tél: $group->tel"),
    array("Facture", "N° RCC(B)", "$user->rcc", substr($group->adresse, 0, 29)." ".$group->cp." ".$group->ville, "Fax: $group->fax"),
    array("Four.de", "N° EAN(P)", "$praticien->ean", "DR.".$praticien->_view, " Tél: $group->tel"),
    array("prestations", "N° RCC(B)", "$praticien->rcc", substr($group->adresse, 0, 29)." ".$group->cp." ".$group->ville, "Fax: $group->fax")
  );
  $font = "vera";
  $pdf->setFont($font, '', 8);
  $pdf->setXY(10, $pdf->getY()-4);
  foreach($lignes as $ligne){
    $pdf->setXY(10, $pdf->getY()+4);
    foreach($ligne as $key => $value){
      $pdf->Cell($colonnes[$key], "", $value);
    }
  }
}
  
foreach($factures as $facture){
  // Création du PDF
  $pdf = new CMbPdf('P', 'mm');
  $pdf->setPrintHeader(false);
  $pdf->setPrintFooter(false);
  $pdf->AddPage();  
  
  $facture->loadRefCoeffFacture();
  $facture->loadRefsFwd();
  $facture->loadRefsBack();
  $facture->loadNumerosBVR("nom");
  $pm = 0;
  $pt = 0;
  foreach($facture->_ref_consults as $consult){
    foreach($consult->_ref_actes_tarmed as $acte){
      $pt += $acte->_ref_tarmed->tp_tl * $acte->_ref_tarmed->f_tl * $acte->quantite;
      $pm += $acte->_ref_tarmed->tp_al * $acte->_ref_tarmed->f_al * $acte->quantite;
    }
  }
  $pt = sprintf("%.2f", $pt * $facture->_coeff);
  $pm = sprintf("%.2f", $pm * $facture->_coeff);
  
  $hauteur_en_tete = 100;
// Praticien selectionné
    if($prat_id){
      $praticien = new CMediusers();
      $praticien->load($prat_id);
    }
    else{
      $praticien = $facture->_ref_chir;
    }
    if(!$praticien){
      $chirSel = CValue::getOrSession("chirSel", "-1");
      $praticien = new CMediusers();
      $praticien->load($chirSel);
    }
  ajoutEntete1($pdf, $facture, $user, $praticien, $group, $colonnes);
  $pdf->setFont("vera", '', 8);
  $tailles_colonnes = array(
            "Date" => 9,
            "Tarif"=> 5,
            "Code" => 7,
            "Code réf" => 7,
            "Sé Cô" => 5,
            "Quantité" => 9,
            "Pt PM/Prix" => 8,
            "fPM" => 5,
            "VPtPM" => 6,
            "Pt PT" => 7,
            "fPT" => 5,
            "VPtPT" => 5,
            "E" => 2,
            "R" => 2,
            "P" => 2,
            "M" => 2,
            "Montant" => 10 );
  $x=0;
  $pdf->setX(10);
  foreach($tailles_colonnes as $key => $value){
    $pdf->setXY($pdf->getX()+$x, 140);
    $pdf->Cell($value, "", $key, null, null, "C");
    $x = $value;
  }
  $ligne = 0;
  $debut_lignes = 140;
  $nb_pages = 1;
  $montant_intermediaire = 0;
  foreach($facture->_ref_consults as $consult){
    foreach($consult->_ref_actes_tarmed as $acte){
      $ligne++;
      $x = 0;
      $pdf->setXY(37, $debut_lignes + $ligne*3);
      //Traitement pour le bas de la page et début de la suivante
      if($pdf->getY()>=265){
      	$pdf->setFont("verab", '', 8);
      	$pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
      	$pdf->Cell(130, "", "Total Intermédiaire", null, null, "R");
      	$pdf->Cell(28, "",$montant_intermediaire , null, null, "R");
      	$pdf->setFont("vera", '', 8);
        $pdf->AddPage();  
        $nb_pages++;
        ajoutEntete2($pdf, $nb_pages, $facture, $user, $praticien, $group, $colonnes);
        $pdf->setXY(10,$pdf->getY()+4);
        $pdf->Cell($colonnes[0]+$colonnes[1], "", "Patient");
        $pdf->Cell($colonnes[2], "", $facture->_ref_patient->nom." ".$facture->_ref_patient->prenom." ".$facture->_ref_patient->naissance);
        $pdf->Line(10, 42, 190, 42);
        $pdf->Line(10, 38, 10, 42);
        $pdf->Line(190, 38, 190, 42);
        $ligne = 0;
        $debut_lignes = 50;
        $pdf->setXY(10,0);          
      }
      $pdf->setFont("verab", '', 7);
      $pdf->setXY(37, $debut_lignes + $ligne*3);
     
      $pdf->Write("<b>",substr($acte->_ref_tarmed->libelle, 0, 90));
      $ligne++;
      //Si le libelle est trop long
      if(strlen($acte->_ref_tarmed->libelle)>90){
      	
      	$pdf->setXY(37, $debut_lignes + $ligne*3);
	      $pdf->Write("<b>",substr($acte->_ref_tarmed->libelle, 90));
	      $ligne++;
      }
     
      
      $pdf->setX(10);
      $pdf->setFont("vera", '', 8);
      
      foreach($tailles_colonnes as $key => $largeur){
      	
          $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
          $valeur = "";
          $cote = "C";
          if($key == "Date"){
            if($acte->date){
              $valeur = $acte->date;
            }
            else{
              $valeur = $consult->_date;
            }
            $valeur= mbTransformTime(null, $valeur, "%d.%m.%Y");
          }
          if($key == "Tarif"){
            $valeur = "001";
          }
          if($key == "Code" && $acte->code!=10){
            $valeur = $acte->code;
          }
          if($key == "Code réf"){
            $valeur = $acte->_ref_tarmed->procedure_associe[0][0];
          }
          if($key == "Sé Cô"){
             $valeur = "1";
          }
          if($key == "Quantité"){
            $valeur = $acte->quantite; //->quantite|string_format:"%.2f"
          }
          if($key == "Pt PM/Prix"){
            $valeur = $acte->_ref_tarmed->tp_al;
            $cote = "R";
          }
          if($key == "fPM"){
            $valeur = $acte->_ref_tarmed->f_al;
          }
          if($key == "VPtPM" || $key == "VPtPT"){
            $valeur = $facture->_coeff;
          }
          if($key == "Pt PT"){
            $valeur = $acte->_ref_tarmed->tp_tl;
            $cote = "R";
          }
          if($key == "fPT"){
            $valeur = $acte->_ref_tarmed->f_tl;
          }
          if($key == "E" || $key == "R"){
            $valeur = "1";
          }
          if($key == "P" || $key == "M"){
            $valeur = "0";
          }
          if($key == "Montant"){
            $pdf->setX($pdf->getX()+3);
            $valeur = sprintf("%.2f", $acte->montant_base * $facture->_coeff);
            $cote = "R";
          }
          $pdf->Cell($largeur, null ,  $valeur, null, null, $cote);
          $x = $largeur;
      }
      $montant_intermediaire += sprintf("%.2f", $acte->montant_base * $facture->_coeff);
    }
    foreach($consult->_ref_actes_caisse as $acte){
      $ligne++;
      $x = 0;
      $pdf->setXY(37, $debut_lignes + $ligne*3);
      //Traitement pour le bas de la page et début de la suivante
      if($pdf->getY()>=265){
      	$pdf->setFont("verab", '', 8);
      	$pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
      	$pdf->Cell(130, "", "Total Intermédiaire", null, null, "R");
      	$pdf->Cell(28, "",$montant_intermediaire , null, null, "R");
      	$pdf->setFont("vera", '', 8);
        $pdf->AddPage();  
        $nb_pages++;
        ajoutEntete2($pdf, $nb_pages, $facture, $user, $praticien, $group, $colonnes);
        $pdf->setXY(10,$pdf->getY()+4);
        $pdf->Cell($colonnes[0]+$colonnes[1], "", "Patient");
        $pdf->Cell($colonnes[2], "", $facture->_ref_patient->nom." ".$facture->_ref_patient->prenom." ".$facture->_ref_patient->naissance);
        $pdf->Line(10, 42, 190, 42);
        $pdf->Line(10, 38, 10, 42);
        $pdf->Line(190, 38, 190, 42);
        $ligne = 0;
        $debut_lignes = 50;
        $pdf->setXY(10,0);          
      }
      $pdf->setFont("verab", '', 7);
      $pdf->setXY(37, $debut_lignes + $ligne*3);
     
      $pdf->Write("<b>",substr($acte->_ref_prestation_caisse->libelle, 0, 90));
      $ligne++;
      //Si le libelle est trop long
      if(strlen($acte->_ref_prestation_caisse->libelle)>90){
      	
      	$pdf->setXY(37, $debut_lignes + $ligne*3);
	      $pdf->Write("<b>",substr($acte->_ref_prestation_caisse->libelle, 90));
	      $ligne++;
      }
     
      
      $pdf->setX(10);
      $pdf->setFont("vera", '', 8);
      
      foreach($tailles_colonnes as $key => $largeur){
      	
          $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
          $valeur = "";
          $cote = "C";
          if($key == "Date"){
            $valeur= mbTransformTime(null,  $consult->_date, "%d.%m.%Y");
          }
          if($key == "Tarif"){
            $valeur = $acte->_ref_caisse_maladie->code;
          }
          if($key == "Code" && $acte->code!=10){
            $valeur = $acte->code;
          }
          if($key == "Sé Cô"){
             $valeur = "1";
          }
          if($key == "Quantité"){
            $valeur = $acte->quantite;
          }
          if($key == "Pt PM/Prix"){
            $valeur = sprintf("%.2f", $acte->montant_base/$acte->quantite);
            $cote = "R";
          }
          if($key == "VPtPM"){
            $valeur = "1.00";
          }
          if($key == "P" || $key == "M"){
            $valeur = "0";
          }
          if($key == "Montant"){
            $pdf->setX($pdf->getX()+3);
            $valeur = $acte->montant_base;
            $cote = "R";
          }
          $pdf->Cell($largeur, null ,  $valeur, null, null, $cote);
          $x = $largeur;
      }
      $montant_intermediaire += $acte->montant_base;
    }
  }
      $pdf->setFont("verab", '', 8);
  $ligne = 265;
  $l = 20;
  $pdf->setXY(20, $ligne+3);
  $pdf->Cell($l, "", "Tarmed PM", null, null, "R");
  $pdf->Cell($l, "", $pm, null, null, "R");
  
  $pdf->setXY(20, $ligne+6);
  $pdf->Cell($l, "", "Tarmed PT", null, null, "R");
  $pdf->Cell($l, "", $pt, null, null, "R");
  
  $x = 3;
  $i = 0;
  $y = 0;
  foreach($facture->_montant_factures_caisse as $key => $value){
  	if(!is_int($key)){
	  	$i++;
	    if($i%2 == 1 ){
	      $x = 3;
	      $y += 70; 
	    }
	    else{ $x = 6;}
	    $pdf->setXY($y, $ligne+$x);
	    $pdf->Cell($l, "", "$key", null, null, "R");
	    $pdf->Cell($l, "", $value, null, null, "R");
  	}
  }
  
  $pdf->setXY(20, $ligne+9);
  $pdf->Cell($l, "", "Montant total/CHF", null, null, "R");
  $pdf->Cell($l, "", $montant_intermediaire, null, null, "R");
  
  $acompte = sprintf("%.2f", $facture->_reglements_total_patient);
  $pdf->Cell(30, "", "Acompte", null, null, "R");
  $pdf->Cell($l, "", "".$acompte, null, null, "R");
  $pdf->Cell($l, "", "", null, null, "R");
  
  $total = $montant_intermediaire - $facture->_reglements_total_patient;
  $pdf->Cell($l, "", "Montant dû", null, null, "R");
  $pdf->Cell($l, "", $total, null, null, "R");

  if($factureconsult_id){
    $pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "I");
  }
  else{
    $pdf->Output(CAppUI::conf("tarmed CCodeTarmed chemin_sauvegarde_bvr").'justificatifs\ '.$facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "F"); 
  }
}
?>