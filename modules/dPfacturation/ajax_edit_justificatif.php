<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();
$colonnes = array(20, 28, 25, 75, 30);

/**
 * Création du premier type d'en-tête possible d'un justificatif 
 * 
 * @param object $pdf         le pdf
 * @param object $facture     la facture courante
 * @param object $colonnes    les colonnes
 * @param object $cle_facture clé de la facture
 * 
 * @return void
 */
function ajoutEntete1($pdf, $facture, $colonnes, $cle_facture){
  ajoutEntete2($pdf, 1, $facture, $colonnes);
  $praticien = $facture->_ref_praticien;
  $function  = $praticien->_ref_function;
  $group     = $function->_ref_group;
  $pdf->SetFillColor(255, 255, 255);
  $pdf->SetDrawColor(0);
  $pdf->Rect(10, 38, 180, 100,'DF');
  $_ref_assurance = "";
  $nom_entreprise = "";
  
  if ($facture->type_facture == "accident" && $facture->assurance_maladie && $facture->_ref_assurance_maladie->employeur) {
    $employeur = new CCorrespondantPatient();
    $employeur->load($facture->_ref_assurance_maladie->employeur);
    $_ref_assurance = $employeur->num_assure;
    $nom_entreprise = $employeur->nom;
  }
  
  $loi = $facture->type_facture == "accident" ? "LAA" : "LAMal";
  $typeRbt = $facture->type_facture == "accident" ? "TP" : "TG";
  if ($facture->cession_creance) {
    $typeRbt .= " avec cession";
  }
  
  $patient = $facture->_ref_patient;
  $assur = array();
  $assurance_patient = null;
  if ($facture->assurance_maladie && !$facture->send_assur_base && $facture->type_facture == "maladie") {
    $assurance_patient = $facture->_ref_assurance_maladie;
  }
  elseif ($facture->assurance_accident && !$facture->send_assur_compl && $facture->type_facture == "accident") {
    $assurance_patient = $facture->_ref_assurance_accident;
  }
  else {
    $assurance_patient = $patient;
  }
  
  $assur["civilite"]  = isset($assurance_patient->civilite) ? ucfirst($patient->civilite) : "";
  $assur["nom"]     = "$assurance_patient->nom $assurance_patient->prenom";
  $assur["adresse"] = "$assurance_patient->adresse";
  $assur["cp"]      = "$assurance_patient->cp $assurance_patient->ville";
  
  $naissance =  mbTransformTime(null, $patient->naissance, "%d.%m.%Y");
  $colonnes = array(20, 28, 25, 25, 25, 50);
  $lignes = array(
    array("Patient"   , "Nom"             , $patient->nom     ,null, "Assurance", $assur["nom"]),
    array(""          , "Prénom"          , $patient->prenom),
    array(""          , "Rue"             , $patient->adresse),
    array(""          , "NPA"             , $patient->cp      , null, $assur["civilite"]),
    array(""          , "Localité"        , $patient->ville   , null, $assur["nom"]),
    array(""          , "Date de naissance",$naissance        , null, $assur["adresse"]),
    array(""          , "Sexe"            , strtoupper($patient->sexe) , null, $assur["cp"]),
    array(""          , "Date cas"        , mbTransformTime(null, $facture->cloture, "%d.%m.%Y")),
    array(""          , "N° cas"          , "$facture->ref_accident"),
    array(""          , "N° AVS"          , $patient->avs),
    array(""          , "N° assuré"       , "$_ref_assurance"),
    array(""          , "Nom entreprise"  , "$nom_entreprise"),
    array(""          , "Canton"          , "GE"),
    array(""          , "Copie"           , "Non"),
    array(""          , "Type de remb."   , $typeRbt),
    array(""          , "Loi"             , "$loi"),
    array(""          , "N° contrat"      , ""),
    array(""          , "Motif traitement", ucfirst($facture->type_facture)),
    array(""          , "Traitement"      , mbTransformTime(null, $facture->_ref_first_consult->_date, "%d.%m.%Y")." - ".mbTransformTime(null, $facture->cloture, "%d.%m.%Y")),
    array(""          , "Rôle/ Localité"  , "-"),
    array("Mandataire", "N° EAN/N° RCC"   , $praticien->ean." - ".$praticien->rcc." "),
    array("Diagnostic", "Contrat"         , "ICD--"),
    array("Liste EAN" , "", "1/".$praticien->ean." 2/".$group->ean),
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
 * @param object $pdf      le pdf
 * @param object $nb       le numéro de la page
 * @param object $facture  la facture courante
 * @param object $colonnes les colonnes
 * 
 * @return void
 */
function ajoutEntete2($pdf, $nb, $facture, $colonnes){
  $praticien = $facture->_ref_praticien;
  $function  = $praticien->_ref_function;
  $group     = $function->_ref_group;
  $font = "verab";
  $pdf->setFont($font, '', 12);
  $pdf->WriteHTML("<h4>Justificatif de remboursement</h4>");
  $font = "vera";
  $pdf->setFont($font, '', 8);
  $pdf->SetFillColor(255, 255, 255);
  $pdf->SetDrawColor(0);
  $pdf->Rect(10, 18, 180,20,'DF');
  $lignes = array(
    array("Document"    , "Identification"  , $facture->_id." ".mbTransformTime(null, null, "%d.%m.%Y %H:%M:%S"), "", "Page $nb"),
    array("Auteur"      , "N° EAN(B)"       , "$group->ean", "$group->_view", " Tél: $group->tel"),
    array("Facture"     , "N° RCC(B)"       , "$group->rcc", substr($group->adresse, 0, 29)." ". $group->cp." ".$group->ville, "Fax: $group->fax"),
    array("Four.de"     , "N° EAN(P)"       , "$praticien->ean", "DR.".$praticien->_view, " Tél: $function->tel"),
    array("prestations" , "N° RCC(B)"       , "$praticien->rcc", substr($function->adresse, 0, 29)." ". $function->cp." ".$function->ville, "Fax: $function->fax")
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
  $function_prat->loadRefGroup();
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
    
    ajoutEntete1($pdf, $facture, $colonnes, $cle_facture);
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
    $tab_actes = array (
      "tarmed" => $facture->_ref_actes_tarmed,
      "caisse" => $facture->_ref_actes_caisse
    );
    foreach ($tab_actes as $keytab => $tab_acte) {
      foreach ($tab_acte as $acte) {
        if (($cle_facture == 0 && $keytab == "tarmed") ||
          ($keytab == "caisse" && ($cle_facture == 1 || ($acte->_class == "CActeCaisse" && $acte->_ref_caisse_maladie->use_tarmed_bill) || 
          ( $acte->_class == "CFactureItem"&& $acte->use_tarmed_bill)))) {
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
            ajoutEntete2($pdf, $nb_pages, $facture, $colonnes);
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
         
          $acte_pm = $acte_coeffpm = $acte_pt = $acte_coeffpt = $code_ref = $libelle = "";
          $coeff_fact = 1;
          $code = "001";
          if ($acte->_class == "CActeTarmed") {
            $libelle = $acte->_ref_tarmed->libelle;
            $code_ref = ($acte->code_ref) ? $acte->code_ref : $acte->_ref_tarmed->procedure_associe[0][0];
            if ($acte->code_ref && (preg_match("/Réduction/", $acte->libelle) || preg_match("/Majoration/", $acte->libelle))) {
              $acte_ref = null;
              foreach ($consult->_ref_actes_tarmed as $acte_tarmed) {
                if ($acte_tarmed->code == $acte->code_ref) {
                  $acte_ref = $acte_tarmed;break;
                }
              }
              $acte_ref->loadRefTarmed();
              $acte_pm = $acte_ref->_ref_tarmed->tp_al;
              $acte_pt = $acte_ref->_ref_tarmed->tp_tl;
              $acte_coeffpm = $acte_ref->_ref_tarmed->f_al;
              $acte_coeffpt = $acte_ref->_ref_tarmed->f_tl;
            }
            else {
              $acte_pm = $acte->_ref_tarmed->tp_al;
              $acte_pt = $acte->_ref_tarmed->tp_tl;
              $acte_coeffpm = $acte->_ref_tarmed->f_al;
              $acte_coeffpt = $acte->_ref_tarmed->f_tl;
            }
            $coeff_fact = $facture->_coeff;
          }
          elseif ($acte->_class == "CActeCaisse") {
            $libelle = $acte->_ref_prestation_caisse->libelle;
            $nom_coeff = "coeff_".$facture->type_facture;
            $coeff = $acte->_ref_caisse_maladie->$nom_coeff;
            $acte_pm = sprintf("%.2f", $acte->_ref_prestation_caisse->pt_medical);
            $acte_pt = sprintf("%.2f", $acte->_ref_prestation_caisse->pt_technique);
            $coeff_fact = $coeff;
          }
          else {
            $libelle = $acte->libelle;
            $acte_pm = $acte->pm;
            $acte_pt = $acte->pt;
            $acte_coeffpm = $acte->coeff_pm;
            $acte_coeffpt = $acte->coeff_pt;
            $coeff_fact = $acte->coeff;
          }
          
          if ($keytab == "caisse") {
            $code = $acte->_class == "CActeCaisse" ? $acte->_ref_caisse_maladie->code : $code = $acte->code_caisse; ;
          }
          
          $pdf->Write("<b>",substr($libelle, 0, 90));
          $ligne++;
          //Si le libelle est trop long
          if (strlen($libelle)>90) {
            $pdf->setXY(37, $debut_lignes + $ligne*3);
            $pdf->Write("<b>",substr($libelle, 90));
            $ligne++;
          }
           
          $pdf->setX(10);
          $pdf->setFont("vera", '', 8);
          foreach ($tailles_colonnes as $key => $largeur) {
            $pdf->setXY($pdf->getX()+$x, $debut_lignes + $ligne*3);
            $valeur = "";
            $cote = "C";
            switch ($key) {
              case "Date" :
                $valeur = $acte->date;
                $valeur= mbTransformTime(null, $valeur, "%d.%m.%Y");
                break;
              case "Tarif":
                $valeur = $code;
                break;
              case "Code réf":
                $valeur = $code_ref;
                break;
              case "Sé Cô":
                $valeur = "1";
                break;
              case "Quantité":
                $valeur = $acte->quantite;
                break;
              case "Pt PM/Prix":
                $valeur = $acte_pm;
                $cote = "R";
                break;
              case "fPM":
                $valeur = $acte_coeffpm;
                break;
              case "VPtPM":
              case "VPtPT":
                $valeur = $coeff_fact;
                break;
              case "Pt PT":
                $valeur = $acte_pt;
                $cote = "R";
                break;
              case "fPT":
                $valeur = $acte_pt;
                break;
              case "Montant":
                $pdf->setX($pdf->getX()+3);
                $valeur = sprintf("%.2f", $acte->montant_base * $coeff_fact);
                $cote = "R";
                break;
              case "E":
              case "R": 
                $valeur = "1"; 
                break;
              case "P":
              case "M":
                $valeur = "0";
                break;
            }
            if ($key == "Code" && $acte->code!=10) {
              $valeur = $acte->code;
            }
            $pdf->Cell($largeur, null ,  $valeur, null, null, $cote);
            $x = $largeur;
          }
          $this_pt = ($acte_pt * $acte_coeffpt * $acte->quantite * $coeff_fact);
          $this_pm = ($acte_pm * $acte_coeffpm * $acte->quantite * $coeff_fact);
          if (round($acte->montant_base, 2) != round(($this_pt + $this_pm)/$coeff_fact, 2)) {
            $this_pt = 0;
            $this_pm = $acte->montant_base * $acte->quantite * $coeff_fact;
          }
          $pt += $this_pt;
          $pm += $this_pm;
          $montant_intermediaire += $this_pt;
          $montant_intermediaire += $this_pm;
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
  
  if ($facture_id) {
    $pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "I");
  }
}
if (!$facture_id) {
  $pdf->Output('Justificatifs.pdf', "I");
}
?>