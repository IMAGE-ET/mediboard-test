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
$user = CMediusers::get();

$facture_class        = CValue::get("facture_class");
$facture_id           = CValue::get("facture_id");
$edition_bvr          = CValue::get("edition_bvr");
$edition_justificatif = CValue::get("edition_justificatif");
$prat_id              = CValue::get("prat_id");
$date_min             = CValue::get("_date_min", mbDate());
$date_max             = CValue::get("_date_max", mbDate());

$factures = array();
$facture = new $facture_class;
//si on a une facture_id on la charge
if ($facture_id) {
  $factures[$facture_id] = $facture->load($facture_id);
}
else {
  $where = array();
  $where["praticien_id"] = " = '$prat_id'";
  $where[]  = "cloture  <= '$date_max' AND cloture >= '$date_min'";
  $factures = $facture->loadList($where, "facture_id DESC", null, "patient_id");
}

if ($edition_bvr) {
  // Création du PDF
  $pdf = new CMbPdf('P', 'mm');
  $pdf->setPrintHeader(false);
  $pdf->setPrintFooter(false);
  
  foreach ($factures as $facture) {
    $facture->loadRefs();
    $pm = 0;
    $pt = 0;
    $autre_tarmed = 0;
    
    foreach ($facture->_ref_actes_tarmed as $acte) {
      if ($acte->_class == "CActeTarmed") {
        if ($acte->_ref_tarmed->tp_al == 0.00 && $acte->_ref_tarmed->tp_tl == 0.00) {
          if ($acte->code_ref && (preg_match("Réduction", $acte->libelle) || preg_match("Majoration", $acte->libelle))) {
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
        $somme = ($acte->_ref_tarmed->tp_tl * $acte->_ref_tarmed->f_tl + $acte->_ref_tarmed->tp_al * $acte->_ref_tarmed->f_al)  * $acte->quantite;
        if ($acte->montant_base != $somme) {
          $pm += $acte->montant_base;
        }
        else {
          $pt += $acte->_ref_tarmed->tp_tl * $acte->_ref_tarmed->f_tl * $acte->quantite;
          $pm += $acte->_ref_tarmed->tp_al * $acte->_ref_tarmed->f_al * $acte->quantite;
        }
      }
      else {
        $pt += $acte->pt * $acte->coeff_pt * $acte->quantite;
        $pm += $acte->pm * $acte->coeff_pm * $acte->quantite;
      }
    }
    
    foreach ($facture->_ref_actes_caisse as $acte) {
      if ($acte->_class == "CActeCaisse") {
        if ($acte->_ref_caisse_maladie->use_tarmed_bill) {
          $autre_tarmed += $acte->montant_base;
        }
      }
      elseif ($acte->use_tarmed_bill) {
        $autre_tarmed += $acte->prix;
      }
    }
    $pt = sprintf("%.2f", $pt * $facture->_coeff);
    $pm = sprintf("%.2f", $pm * $facture->_coeff);
    
    $pre_tab = array();
    $pre_tab["Medical:"]  = $pm;
    $pre_tab["Tarmed:"]   = $pt;
    $pre_tab["Autres:"]   = sprintf("%.2f", $facture->_montant_sans_remise - $pm - $pt - $autre_tarmed);
    
    // Praticien selectionné
    $praticien = $facture->_ref_praticien;
    $function_prat = $praticien->loadRefFunction();
    $adherent = $praticien->adherent;
    
    $acompte = 0;
    $nb_factures = count($facture->_montant_factures_caisse);
    $num_fact = 0;
    
    foreach ($facture->_montant_factures_caisse as $cle_facture => $montant_facture) {
      if ($acompte < $facture->_montant_avec_remise) {
        //Création de la page de la facture
        $pdf->AddPage();
        $colonne1 = 10;
        $colonne2 = 120;
        
        $font = "verab";

        // A
        $pdf->setFont($font, '', 12);
        $pdf->WriteHTML("<h4>Facture du patient</h4>");

        // B
        $font = "vera";
        $pdf->setFont($font, '', 6);
        $pdf->setXY($colonne1, 17);  
        $pdf->Write("", "Cette page est pour vos archives");
        $pdf->setXY($colonne1, $pdf->GetY()+4);
        $pdf->Write("", "Veuillez envoyer le justificatif de remboursement");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Write("", "annexé à votre caisse maladie ou à l'assurance");
        
        $pdf->setFont($font, '', 8);
        
        // C + D : Auteur de la facture
        if (stristr($function_prat->adresse, "\r\n")) {
          $adresse_part1 = stristr($function_prat->adresse, "\r\n", true);
          $adresse_part2 = stristr($function_prat->adresse, "\r\n");
          $adresse_part2 = str_replace("\r\n",'',$adresse_part2);
        }
        else {
          $adresse_part1 = substr($function_prat->adresse, 0, 30);
          $adresse_part2 = substr($function_prat->adresse, 30);
        }
        if (strlen($function_prat->cp)>4) {
          $function_prat->cp =  substr($function_prat->cp, 1);
        }
        if (strlen($facture->_ref_patient->cp)>4) {
          $facture->_ref_patient->cp =  substr($facture->_ref_patient->cp, 1);
        }
        $auteur = array(
          "50" => "Auteur facture",
          $user->_view,
          "$function_prat->_view",
          $adresse_part1,
          $adresse_part2,
          $function_prat->cp." ".$function_prat->ville,
          "80" => "Four. de prestations",
          "Dr. ".$praticien->_view,
          "$function_prat->_view",
          $adresse_part1,
          $adresse_part2,
          $function_prat->cp." ".$function_prat->ville,
        );
        $tab[$colonne1] = $auteur;

        // E
        $adresse1 = $adresse2 = "";
        if (stristr($facture->_ref_patient->adresse, "\r\n")) {
          $adresse1 = stristr($facture->_ref_patient->adresse, "\r\n", true);
          $adresse2 = stristr($facture->_ref_patient->adresse, "\r\n");
          $adresse2 = str_replace("\r\n", '', $adresse2);
        }
        else {
          $adresse1 = substr($facture->_ref_patient->adresse, 0, 30);
          $adresse2 = substr($facture->_ref_patient->adresse, 30);
        }
        $destinataire = array(
          "nom"=> $facture->_ref_patient->_view,
          "adresse1"=> $adresse1,
          "adresse2"=> $adresse2,
          "cp"=> $facture->_ref_patient->cp." ".$facture->_ref_patient->ville,
        );
         
//        if ($facture->cession_creance || $facture->type_facture == "accident") {
          $correspondant = new CCorrespondantPatient();
          
          if ($facture->assurance_maladie && $cle_facture == 0 && !$facture->send_assur_base) {
            $correspondant->load($facture->assurance_maladie);
          }
          elseif ($facture->assurance_accident && $cle_facture == 1 && !$facture->send_assur_compl) {
            $correspondant->load($facture->assurance_accident);
          }
          
          if ($correspondant->_id) {
            if (strlen($correspondant->cp)>4) {
              $correspondant->cp = substr($correspondant->cp, 1);
            }
            $destinataire["nom"] = $correspondant->nom." ".$correspondant->prenom;
            if (stristr($correspondant->adresse, "\r\n")) {
              $destinataire["adresse1"] = stristr($correspondant->adresse, "\r\n", true);
              $destinataire["adresse2"] = stristr($correspondant->adresse, "\r\n");
              $destinataire["adresse2"] = str_replace("\r\n",'',$destinataire["adresse2"]);
            }
            else {
              $destinataire["adresse1"] = $correspondant->adresse;
              $destinataire["adresse2"] = "";
            }
            $destinataire["cp"] =  $correspondant->cp." ".$correspondant->ville;
          }
//        }
        
        // E + F : Destinataire de la facture
        $patient = array(
          "50" => "Destinataire",
          $destinataire["nom"],
          $destinataire["adresse1"],
          $destinataire["adresse2"],
          $destinataire["cp"],
          "80" => "Patient",
          "n° AVS: ".$facture->_ref_patient->avs, 
          $facture->_ref_patient->_view,
          $adresse1,
          $adresse2,
          $facture->_ref_patient->cp." ".$facture->_ref_patient->ville
        );
        
        $tab[$colonne2] = $patient;
        $pdf->SetTextColor(80,80,80);
        if ($facture->_reglements_total_patient) {
          $pdf->setFont($font, '', 25);
          $pdf->setXY(100,20);
          $pdf->Write("", "DUPLICATA");
        }
        if ($facture->type_facture == "accident") {
          $pdf->setFont($font, '', 15);
          $pdf->setXY(80,40);
          $pdf->Write("", "Accident");
        }
        if ($facture->cession_creance) {
          $pdf->setFont($font, '', 15);
          $pdf->setXY(80,30);
          $pdf->Write("", "Cession de créance");
        }
        $pdf->SetTextColor(0,0,0);
        $pdf->setFont($font, '', 8);

        // Ecriture de C, D, E, F
        $x = $y = 0;
        foreach ($tab as $k => $v) {
          foreach ($v as $key => $value) {
            if ($value) {
              if ($key == "50" || $key == "80" ) {
                $y = $key;
                $x=0;
              }
              $pdf->setXY($k, $y+$x);
              $pdf->Cell(30, "", $value);
              $x = ($key == "50" || $key == "80") ? $x+5 : $x+3;
            }
          }
        }
        
        // G : Données de la facture
        $pdf->SetDrawColor(0);
        $pdf->Line($colonne1, 122, $colonne1+40, 122);
        $pdf->setXY($colonne1, 120);
        $pdf->Cell(25, "", "Données de la facture", null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+5);
        $pdf->Cell(22, "", "Date facture:", null, null, "R");
        $pdf->Cell(25, "", mbTransformTime(null, $facture->cloture, "%d %B %Y"), null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Cell(22, "", "N° facture:", null, null, "R");
        $pdf->Cell(25, "", $facture->_id, null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Cell(22, "", "Traitement du:", null, null, "R");
        $pdf->Cell(25, "", mbTransformTime(null, $facture->_ref_first_consult->_date, "%d %B %Y"), null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Cell(22, "", "au:", null, null, "R");
        $pdf->Cell(25, "", mbTransformTime(null, $facture->cloture, "%d %B %Y"), null, null, "L");
  
        $montant_facture = sprintf('%0.2f', $montant_facture);
        if ($montant_facture < 0) {
          $montant_facture = sprintf('%0.2f', 0);
        }
        
        // H : Tarif
        $title_montant = "";
        if ($nb_factures>1) {
          $num_fact++;
          $title_montant = "n° ".$num_fact;
        }
        
        $montant_total = 0;
        $tarif = array( "Tarif"         => "CHF");
        foreach ($pre_tab as $cles => $valeur) {
          if (($cle_facture == 0 && $cles != "Autres:") || ($cle_facture == 1 && $cles == "Autres:")) {
              $tarif[$cles] = $valeur;
              $montant_total += $valeur;
          }
          elseif ($cle_facture == 0) {
            $tarif[$cles] = sprintf('%0.2f', $autre_tarmed);
            $montant_total += sprintf('%0.2f', $autre_tarmed);
          }
          else {
              $tarif[$cles] = "0.00";
          }
        }
        $tarif["Remise:"]         = sprintf('%0.2f', -$facture->remise);
        $tarif["Montant total:"]  = sprintf('%0.2f', $montant_total);
        $tarif["Acompte:"]        = "0.00";
        $tarif["Montant dû $title_montant:"]  = $montant_facture;
        
        $acompte += $montant_facture;
        $pdf->Line($colonne2, 122, $colonne2+50, 122);
        
        $x = 0;
        foreach ($tarif as $key => $value) {
          $pdf->setXY($colonne2, 120+$x);
          $pdf->Cell(25, "", $key, null, null, "R");
          $pdf->Cell(22, "", $value, null, null, "R");
          if ($key == "Tarif" || $key == "Remise:") {
            $x+=5;
            if ($key == "Remise:") {
              $pdf->Line($colonne2, 117 +$x, $colonne2+50, 117 +$x);
              $font = "verab";
              $pdf->setFont($font, '', 8);
            }
          }
          else {
            $x+=3;
          }
        }
      
        //le 01 sera fixe car il correspond à un "Codes des genres de justificatifs (BC)" ici :01 = BVR en CHF
        $genre = "01";
        $montant = sprintf('%010d', $montant_facture*100);
        $cle = $facture->getNoControle($genre.$montant);
        $adherent2 = str_replace(' ','',$praticien->adherent);
        $adherent2 = str_replace('-','',$adherent2);
        $_num_reference = str_replace(' ','',$facture->num_reference);
        $bvr = $genre.$montant.$cle.">".$_num_reference."+ ".$adherent2.">";
          
        // Dimensions du bvr
        $largeur_bvr = 210;
        $hauteur_bvr = 106;
        $haut_doc = 297-$hauteur_bvr;

        // Une ligne = 1/6 pouce = 4.2333 mm
        $h_ligne = 4.2333; // $hauteur_bvr/25;

        // Une colonne = 1/10 pouce = 2.54 mm
        $l_colonne = 2.54; // $largeur_bvr/83;
        
        //Police par Défault du BVR
        $font = "vera";

        $left_offset = 84 * $l_colonne - $largeur_bvr;

        //Boucle utilisée pour dupliquer les Partie1 et 2 avec un décalage de colonnes
        for ($i = 0; $i<=1; $i++) {
          $decalage = $i*24*$l_colonne + $left_offset;
          
          //Adresse du patient
          $pdf->SetTextColor(0);
          $pdf->setFont($font, '', 8);
          $pdf->Text($l_colonne + $decalage, $h_ligne*3+$haut_doc , $praticien->_view);
          $pdf->Text($l_colonne + $decalage, $h_ligne*4+$haut_doc , $function_prat->_view);
          $j = 1;
          $pdf->Text($l_colonne + $decalage, $h_ligne*5+$haut_doc , $adresse_part1);
          if ($adresse_part2) {
            $pdf->Text($l_colonne + $decalage, $h_ligne*6+$haut_doc , $adresse_part2);
            $j = 2;
          }
          $pdf->Text($l_colonne + $decalage, $h_ligne*(5+$j)+$haut_doc , $function_prat->cp." ".$function_prat->ville);

          //Numéro adhérent, CHF, Montant1 et Montant2
          $pdf->Text($l_colonne*11 + $decalage, $h_ligne*10.75+$haut_doc , $adherent);

          $pdf->setFont($font, '', 10);
          $pdf->Text($l_colonne*(17-strlen($montant_facture*100)) + $decalage, $h_ligne*13+$haut_doc , sprintf("%d", $montant_facture));
          
          $cents = floor(sprintf("%.2f", $montant_facture - sprintf("%d", $montant_facture))*100);
          if ($cents<10) {			
            $cents = "0".$cents;
          }
          $pdf->Text($l_colonne*19 + $decalage, $h_ligne*13+$haut_doc , $cents);
        }
        
        $decalage = $left_offset; // 7.36 // 8;

        //écriture de la référence
        $num_reference = preg_replace("/^(\d{2})(\d{5})(\d{5})(\d{5})(\d{5})$/", '\\1 \\2 \\3 \\4 \\5 \\6', $facture->num_reference);
        $pdf->setFont($font, '', 11);
        $pdf->Text(50*$l_colonne, $h_ligne*8.75+$haut_doc , $num_reference);

        $pdf->setFont($font, '', 8);
        $pdf->Text($l_colonne + $decalage, $h_ligne*15+$haut_doc , $facture->num_reference);
        //Adresse du patient de la facture
        $pdf->Text($l_colonne + $decalage, $h_ligne*16+$haut_doc , $destinataire["nom"]);
        $pdf->Text(49*$l_colonne + $decalage, $h_ligne*12+$haut_doc , $destinataire["nom"]);
        
        $pdf->Text($l_colonne + $decalage, $h_ligne*17+$haut_doc , $destinataire["adresse1"]);
        $pdf->Text(49*$l_colonne + $decalage, $h_ligne*13+$haut_doc , $destinataire["adresse1"]);
        $j = 1;
        if ($adresse2) {
          $pdf->Text($l_colonne + $decalage, $h_ligne*(18)+$haut_doc , $destinataire["adresse2"]);
          $pdf->Text(49*$l_colonne + $decalage, $h_ligne*14+$haut_doc , $destinataire["adresse2"]);
          $j = 2;
        }
        
        $pdf->Text($l_colonne + $decalage, $h_ligne*(17+$j)+$haut_doc , $destinataire["cp"]);
        $pdf->Text(49*$l_colonne + $decalage, $h_ligne*(13+$j)+$haut_doc , $destinataire["cp"]);
        
        //Ecriture du code bvr généré modulo10 récursif
        $font = "ocrbb";
        $pdf->setFont($font, '', 12);
        
        $w = (80- strlen($bvr)) *$l_colonne - $decalage; 
        $pdf->Text($w, $h_ligne*21+$haut_doc, $bvr);
      }
    }
    //enregistrement pour chaque facture l'ensemble des factures
    if ($facture_id) {
      $pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "I");
    }
  }
  if (!$facture_id) {
    $pdf->Output('Factures.pdf', "I");
  }
}
if ($edition_justificatif) {
  include "ajax_edit_justificatif.php" ;
}

?>