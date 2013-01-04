<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$colonnes = array(20, 28, 25, 75, 30);

/**
 * Création du premier type d'en-tête possible d'un justificatif 
 * 
 * @param object $pdf         le pdf
 * @param object $facture     la facture courante
 * @param object $user        l'utilisateur
 * @param object $praticien   le praticien de la facture
 * @param object $group       l'établissement
 * @param object $colonnes    les colonnes
 * @param object $cle_facture clé de la facture
 * 
 * @return void
 */
function ajoutEntete1($pdf, $facture, $user, $praticien, $group, $colonnes, $cle_facture){
  ajoutEntete2($pdf, 1, $facture, $user, $praticien, $group, $colonnes);
  $pdf->SetFillColor(255, 255, 255);
  $pdf->SetDrawColor(0);
  $pdf->Rect(10, 38, 180, 100,'DF');
  $_ref_assurance = "";
  $nom_entreprise = "";
  
  if ($facture->type_facture == "accident" && $facture->assurance_base && $facture->_ref_assurance_base->employeur) {
    $employeur = new CCorrespondantPatient();
    $employeur->load($facture->_ref_assurance_base->employeur);
    $_ref_assurance = $employeur->num_assure;
    $nom_entreprise = $employeur->nom;
  }
  $typeRbt = "TG";
  if (($facture->assurance_base && $cle_facture == 0 && !$facture->send_assur_base) 
      || ($facture->assurance_complementaire && $cle_facture == 1 && !$facture->send_assur_compl)) {
    $typeRbt = "TP";
  }
  $loi = "LAMal";
  if ($facture->cession_creance) {
    //La LAI : Loi sur l'Assurance Invalidité
    $loi = "LAI";
  }
  elseif ($facture->type_facture == "accident") {
    //La LAA : Loi sur L'Assurance Accident
    $loi = "LAA";
  }
  $lignes = array(
    array("Patient"   , "Nom", $facture->_ref_patient->nom),
    array(""          , "Prénom", $facture->_ref_patient->prenom),
    array(""          , "Rue", $facture->_ref_patient->adresse),
    array(""          , "NPA",  $facture->_ref_patient->cp),
    array(""          , "Localité", $facture->_ref_patient->ville),
    array(""          , "Date de naissance", mbTransformTime(null, $facture->_ref_patient->naissance, "%d.%m.%Y")),
    array(""          , "Sexe", $facture->_ref_patient->sexe),
    array(""          , "Date cas", mbTransformTime(null, $facture->cloture, "%d.%m.%Y")),
    array(""          , "N° cas", "$facture->ref_accident"),
    array(""          , "N° AVS", $facture->_ref_patient->avs),
    array(""          , "N° assuré", "$_ref_assurance"),
    array(""          , "Nom entreprise", "$nom_entreprise"),
    array(""          , "Canton", "GE"),
    array(""          , "Copie", "Non"),
    array(""          , "Type de remb.", $typeRbt),
    array(""          , "Loi", "$loi"),
    array(""          , "N° contrat", ""),
    array(""          , "Motif traitement", "$facture->type_facture"),
    array(""          , "Traitement", mbTransformTime(null, $facture->_ref_first_consult->_date, "%d.%m.%Y")." - ".mbTransformTime(null, $facture->cloture, "%d.%m.%Y")),
    array(""          , "Rôle/ Localité", "-"),
    array("Mandataire", "N° EAN/N° RCC", $praticien->ean." - ".$praticien->rcc." "),
    array("Diagnostic", "Contrat", "ICD--"),
    array("Liste EAN" , "", "1/".$praticien->ean." 2/".$user->ean),
    array("Commentaire")
  );
  $font = "vera";
  $pdf->setFont($font, '', 8);
  foreach ($lignes as $ligne) {
    $pdf->setXY(10, $pdf->getY()+4);
    foreach ($ligne as $key => $value) {
      $pdf->Cell($colonnes[$key], "", $value);
    }
  }
  $pdf->Line(10, 119, 190, 119);
  $pdf->Line(10, 123, 190, 123);
  $pdf->Line(10, 127, 190, 127);
  $pdf->Line(10, 131, 190, 131);
}

/**
 * Création du second type d'en-tête possible d'un justificatif, celui-ci étant plus léger 
 * 
 * @param object $pdf       le pdf
 * @param object $nb        le numéro de la page
 * @param object $facture   la facture courante
 * @param object $user      l'utilisateur
 * @param object $praticien le praticien de la facture
 * @param object $group     l'établissement
 * @param object $colonnes  les colonnes
 * 
 * @return void
 */
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
    array("Facture", "N° RCC(B)", "$user->rcc", substr($group->adresse, 0, 29)." ". $group->cp." ".$group->ville, "Fax: $group->fax"),
    array("Four.de", "N° EAN(P)", "$praticien->ean", "DR.".$praticien->_view, " Tél: $group->tel"),
    array("prestations", "N° RCC(B)", "$praticien->rcc", substr($group->adresse, 0, 29)." ". $group->cp." ".$group->ville, "Fax: $group->fax")
  );
  $font = "vera";
  $pdf->setFont($font, '', 8);
  $pdf->setXY(10, $pdf->getY()-4);
  foreach ($lignes as $ligne) {
    $pdf->setXY(10, $pdf->getY()+4);
    foreach ($ligne as $key => $value) {
      $pdf->Cell($colonnes[$key], "", $value);
    }
  }
}

// Création du PDF
$pdf = new CMbPdf('P', 'mm');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

foreach ($factures as $facture) {
  $facture->loadRefs();
  $praticien = $facture->_ref_praticien;
  $function_prat = $praticien->loadRefFunction();
  $function_prat->adresse = str_replace("\r\n",' ', $function_prat->adresse);
  $facture->_ref_patient->adresse = str_replace("\r\n",' ', $facture->_ref_patient->adresse);
  
  if (strlen($facture->_ref_patient->cp)>4) {
    $facture->_ref_patient->cp = substr($facture->_ref_patient->cp, 1);
  }
  if (strlen($function_prat->cp)>4) {
    $function_prat->cp = substr($function_prat->cp, 1);
  }
  
  foreach ($facture->_montant_factures_caisse as $cle_facture => $montant_facture) {
    $pdf->AddPage();
    $pm = $pt = 0;
    
    ajoutEntete1($pdf, $facture, $user, $praticien, $function_prat, $colonnes, $cle_facture);
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
    foreach ($tailles_colonnes as $key => $value) {
      $pdf->setXY($pdf->getX()+$x, 140);
      $pdf->Cell($value, "", $key, null, null, "C");
      $x = $value;
    }
    $ligne = 0;
    $debut_lignes = 140;
    $nb_pages = 1;
    $montant_intermediaire = 0;
    foreach ($facture->_ref_consults as $consult) {
      if ($cle_facture == 0) {
        foreach ($consult->_ref_actes_tarmed as $acte) {
          $ligne++;
          $x = 0;
          $pdf->setXY(37, $debut_lignes + $ligne*3);
          //Traitement pour le bas de la page et début de la suivante
          if ($pdf->getY()>=265) {
            $pdf->setFont("verab", '', 8);
            $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
            $pdf->Cell(130, "", "Total Intermédiaire", null, null, "R");
            $pdf->Cell(28, "",$montant_intermediaire , null, null, "R");
            $pdf->setFont("vera", '', 8);
            $pdf->AddPage();  
            $nb_pages++;
            ajoutEntete2($pdf, $nb_pages, $facture, $user, $praticien, $function_prat, $colonnes);
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
          if (strlen($acte->_ref_tarmed->libelle)>90) {
            $pdf->setXY(37, $debut_lignes + $ligne*3);
            $pdf->Write("<b>",substr($acte->_ref_tarmed->libelle, 90));
            $ligne++;
          }
         
          $pdf->setX(10);
          $pdf->setFont("vera", '', 8);
        
          if ($acte->_ref_tarmed->tp_al == 0.00 && $acte->_ref_tarmed->tp_tl == 0.00) {
            if ($acte->code_ref && (preg_match("/Réduction/", $acte->libelle) || preg_match("Majoration", $acte->libelle)) ) {
              $acte_ref = null;
              foreach ($consult->_ref_actes_tarmed as $acte_tarmed) {
                if ($acte_tarmed->code == $acte->code_ref) {
                  $acte_ref = $acte_tarmed;break;
                }
              }
              $acte_ref->loadRefTarmed();
              $acte->_ref_tarmed->tp_al = $acte_ref->_ref_tarmed->tp_al;
              $acte->_ref_tarmed->tp_tl = $acte_ref->_ref_tarmed->tp_tl;
            }
            elseif ($acte->montant_base) {
              $acte->_ref_tarmed->tp_al = $acte->montant_base;
            }
          }
          foreach ($tailles_colonnes as $key => $largeur) {      	
              $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
              $valeur = "";
              $cote = "C";
              if ($key == "Date") {
                $valeur = ($acte->date) ? $acte->date : $consult->_date;
                $valeur= mbTransformTime(null, $valeur, "%d.%m.%Y");
              }
              if ($key == "Tarif") {
                $valeur = "001";
              }
              if ($key == "Code" && $acte->code!=10) {
                $valeur = $acte->code;
              }
              if ($key == "Code réf") {
                $valeur = ($acte->code_ref) ? $acte->code_ref : $acte->_ref_tarmed->procedure_associe[0][0];
              }
              if ($key == "Sé Cô") {
                 $valeur = "1";
              }
              if ($key == "Quantité") {
                $valeur = $acte->quantite;
              }
              
              if ($acte->code_ref && (preg_match("/Réduction/", $acte->libelle) || preg_match("/Majoration/", $acte->libelle))) {
                $acte_ref = null;
                foreach ($consult->_ref_actes_tarmed as $acte_tarmed) {
                  if ($acte_tarmed->code == $acte->code_ref) {
                    $acte_ref = $acte_tarmed;break;
                  }
                }
                $acte_ref->loadRefTarmed();
                $acte->_ref_tarmed->tp_al = $acte_ref->_ref_tarmed->tp_al;
                $acte->_ref_tarmed->tp_tl = $acte_ref->_ref_tarmed->tp_tl;
              }
              if ($key == "Pt PM/Prix") {
                $valeur = $acte->_ref_tarmed->tp_al;
                $cote = "R";
              }
              if ($key == "fPM") {
                $valeur = $acte->_ref_tarmed->f_al;
              }
              if ($key == "VPtPM" || $key == "VPtPT") {
                $valeur = $facture->_coeff;
              }
              if ($key == "Pt PT") {
                $valeur = $acte->_ref_tarmed->tp_tl;
                $cote = "R";
              }
              if ($key == "fPT") {
                $valeur = $acte->_ref_tarmed->f_tl;
              }
              if ($key == "E" || $key == "R") {
                $valeur = "1";
              }
              if ($key == "P" || $key == "M") {
                $valeur = "0";
              }
              if ($key == "Montant") {
                $pdf->setX($pdf->getX()+3);
                $valeur = sprintf("%.2f", $acte->montant_base * $facture->_coeff);
                $cote = "R";
              }
              $pdf->Cell($largeur, null ,  $valeur, null, null, $cote);
              $x = $largeur;
          }
          $this_pt = ($acte->_ref_tarmed->tp_tl * $acte->_ref_tarmed->f_tl * $acte->quantite * $facture->_coeff);
          $this_pm = ($acte->_ref_tarmed->tp_al * $acte->_ref_tarmed->f_al * $acte->quantite * $facture->_coeff);
          if (round($acte->montant_base, 2) != round(($this_pt + $this_pm)/$facture->_coeff, 2)) {
            $this_pt = 0;
            $this_pm = $acte->montant_base * $acte->quantite *$facture->_coeff;
          }
          $pt += $this_pt;
          $pm += $this_pm;
          $montant_intermediaire += $this_pt;
          $montant_intermediaire += $this_pm;
        }
      } 
      foreach ($consult->_ref_actes_caisse as $acte) {
        if ($cle_facture == 1 || $acte->_ref_caisse_maladie->use_tarmed_bill) {
          $ligne++;
          $x = 0;
          $pdf->setXY(37, $debut_lignes + $ligne*3);
          //Traitement pour le bas de la page et début de la suivante
          if ($pdf->getY()>=265) {
            $pdf->setFont("verab", '', 8);
            $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
            $pdf->Cell(130, "", "Total Intermédiaire", null, null, "R");
            $pdf->Cell(28, "",$montant_intermediaire , null, null, "R");
            $pdf->setFont("vera", '', 8);
            $pdf->AddPage();
            $nb_pages++;
            ajoutEntete2($pdf, $nb_pages, $facture, $user, $praticien, $function_prat, $colonnes);
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
          if (strlen($acte->_ref_prestation_caisse->libelle)>90) {      	
            $pdf->setXY(37, $debut_lignes + $ligne*3);
            $pdf->Write("<b>",substr($acte->_ref_prestation_caisse->libelle, 90));
            $ligne++;
          }
          
          $pdf->setX(10);
          $pdf->setFont("vera", '', 8);
          
          $nom_coeff = "coeff_".$facture->type_facture;
          $coeff = $acte->_ref_caisse_maladie->$nom_coeff;
          
          foreach ($tailles_colonnes as $key => $largeur) {
              $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
              $valeur = "";
              $cote = "C";
              if ($key == "Date") {
                $valeur = ($acte->date) ? $acte->date : $consult->_date;
                $valeur= mbTransformTime(null, $valeur, "%d.%m.%Y");
              }
              if ($key == "Tarif") {
                $valeur = $acte->_ref_caisse_maladie->code;
              }
              if ($key == "Code" && $acte->code!=10) {
                $valeur = $acte->code;
              }
              if ($key == "Sé Cô") {
                 $valeur = "1";
              }
              if ($key == "Quantité") {
                $valeur = $acte->quantite;
              }
              if ($key == "Pt PM/Prix") {
                $valeur = sprintf("%.2f", $acte->_ref_prestation_caisse->pt_medical);
                $cote = "R";
              }
              if ($key == "VPtPM" || $key == "VPtPT") {
                $valeur = $coeff;
              }
              if ($key == "Pt PT") {
                $valeur = sprintf("%.2f", $acte->_ref_prestation_caisse->pt_technique);
                $cote = "R";
              }
              if ($key == "P" || $key == "M") {
                $valeur = "0";
              }
              if ($key == "Montant") {
                $pdf->setX($pdf->getX()+3);
                $valeur = sprintf("%.2f", $acte->montant_base * $coeff);
                $cote = "R";
              }
              $pdf->Cell($largeur, null ,  $valeur, null, null, $cote);
              $x = $largeur;
          }
          $montant_intermediaire += sprintf("%.2f", $acte->montant_base * $coeff);
        }
      }
    }
    
    $pt = sprintf("%.2f", $pt);
    $pm = sprintf("%.2f", $pm);
    
    $pdf->setFont("verab", '', 8);
    $ligne = 265;
    $l = 20;
    $pdf->setXY(20, $ligne+3);
    $pdf->Cell($l, "", "Tarmed PM", null, null, "R");
    $pdf->Cell($l, "", $pm, null, null, "R");
    
    $pdf->setXY(20, $ligne+6);
    $pdf->Cell($l, "", "Tarmed PT", null, null, "R");
    $pdf->Cell($l, "", $pt, null, null, "R");
        
    $autre_temp = $cle_facture == 0 ? $montant_facture - $pm - $pt : $montant_facture;
    $autre_temp = round("%.2f", $autre_temp);
    $autre = ($autre_temp <= 0.05) ? 0.00 : $autre_temp;
    $pdf->setXY(70, $ligne+3);
    $pdf->Cell($l, "", "Autres", null, null, "R");
    $pdf->Cell($l, "",  sprintf("%.2f", $autre), null, null, "R");
    
    $pdf->setXY(20, $ligne+9);
    $pdf->Cell($l, "", "Montant total/CHF", null, null, "R");
    $pdf->Cell($l, "", sprintf("%.2f",$montant_intermediaire), null, null, "R");
    
    $acompte = sprintf("%.2f", $facture->_reglements_total_patient);
    $pdf->Cell(30, "", "Acompte", null, null, "R");
    $pdf->Cell($l, "", "".$acompte, null, null, "R");
    $pdf->Cell($l, "", "", null, null, "R");
    
    $total_temp = $montant_intermediaire - $facture->_reglements_total_patient;
    $total = $total_temp<0 ? 0.00 : $total_temp;
    
    $pdf->Cell($l, "", "Montant dû", null, null, "R");
    $pdf->Cell($l, "", sprintf("%.2f",$total), null, null, "R");
  }
  
  if ($factureconsult_id) {
    $pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "I");
  }
  
//  else {
//    $exchange_source = CExchangeSource::get("tarmed_export_impression_justificatifs", "ftp", true);
//    $exchange_source->init();
//    try {
//      $exchange_source->setData($pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "S"));
//      $exchange_source->send("", $facture->cloture."_".$facture->_ref_patient->nom.'.pdf');
//    } catch(CMbException $e) {
//      $e->stepAjax();
//    }
//  }
}
if (!$factureconsult_id) {
  $pdf->Output('Justificatifs.pdf', "I");
}
?>