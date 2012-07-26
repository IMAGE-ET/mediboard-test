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

CCanDo::checkEdit();

$factureconsult_id    = CValue::get("factureconsult_id");
$edition_bvr          = CValue::get("edition_bvr");
$edition_justificatif = CValue::get("edition_justificatif");
$prat_id              = CValue::get("prat_id");
$date_min             = CValue::get("_date_min", mbDate());
$date_max             = CValue::get("_date_max", mbDate());

$group = CGroups::loadCurrent();
$user = CMediusers::get();

$factures = array();
$facture = new CFactureConsult();
//si on a une factureconsult_id on la charge
if ($factureconsult_id) {
  $factures[$factureconsult_id] = $facture->load($factureconsult_id);
}
else {
  $where = array();
  $where[]  = "(ouverture >= '$date_min' AND cloture  <= '$date_max') OR (ouverture >= '$date_min' AND cloture  <= '$date_max') ";
  $factures = $facture->loadList($where, "factureconsult_id DESC", null, "patient_id");
  
  //Avant l'envoi par ftp des fichiers, création d'un fichier print.lock indiquant un envoi de fichiers en cours
  $exchange_source = CExchangeSource::get("tarmed_export_impression_factures", "ftp", true);
  $exchange_source->init();
  try {
    $exchange_source->setData("Ne pas imprimer maintenant. Merci d'avance!");
    $exchange_source->send("", "print.lock");
  } catch(CMbException $e) {
    $e->stepAjax();
  }
}

if ($edition_bvr) {
  foreach ($factures as $facture) {
    $facture->loadRefCoeffFacture();
    $facture->loadRefsFwd();
    $facture->loadRefsBack();
    $facture->loadNumerosBVR("nom");
      
    // Création du PDF
    $pdf = new CMbPdf('P', 'mm');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    $pm = 0;
    $pt = 0;
    foreach ($facture->_ref_consults as $consult) {
      foreach ($consult->_ref_actes_tarmed as $acte) {
        if ($acte->code_ref && $acte->_ref_tarmed->tp_tl == 0.00&& $acte->_ref_tarmed->tp_al == 0.00) {
          $acte_ref = null;
          foreach ($consult->_ref_actes_tarmed as $acte_tarmed) {
            if ($acte_tarmed->code == $acte->code_ref) {
              $acte_tarmed->loadRefTarmed();
              $acte_ref = $acte_tarmed;break;
            }
          }
          $acte->_ref_tarmed->tp_al = $acte_ref->_ref_tarmed->tp_al;
          $acte->_ref_tarmed->tp_tl = $acte_ref->_ref_tarmed->tp_tl;          
        }
        
        $pt += $acte->_ref_tarmed->tp_tl * $acte->_ref_tarmed->f_tl * $acte->quantite;
        $pm += $acte->_ref_tarmed->tp_al * $acte->_ref_tarmed->f_al * $acte->quantite;
      }
    }
    $pt = sprintf("%.2f", $pt * $facture->_coeff);
    $pm = sprintf("%.2f", $pm * $facture->_coeff);
    $facture->_ref_patient->adresse = str_replace(CHR(13).CHR(10),' ', $facture->_ref_patient->adresse);
    
    $pre_tab = array();
    $pre_tab["Medical:"]  = $pm;
    $pre_tab["Tarmed:"]   = $pt;
    $total_pre_tab = 0;
    
    foreach ($facture->_montant_factures_caisse as $key => $value) {
      if (!is_int($key)) {
        $pre_tab["$key:"]   = $value;
      }
    }
    
    if (count($pre_tab) == 2 && count($facture->_montant_factures_caisse) > 1) {
      $pre_tab["Autres:"]   = sprintf("%.2f", $facture->_montant_sans_remise - $pm - $pt);
    }
    
    // Praticien selectionné
    if ($prat_id) {
      $praticien = new CMediusers();
      $praticien->load($prat_id);
    }
    else {
      $praticien = $facture->_ref_chir;
    }
    if (!$praticien) {
      $chirSel = CValue::getOrSession("chirSel", "-1");
      $praticien = new CMediusers();
      $praticien->load($chirSel);
    }
    
    $adherent = $praticien->adherent;
    $group->adresse = str_replace(CHR(13).CHR(10),' ', $group->adresse);
    
    $acompte = 0;
    $nb_factures = count($facture->_montant_factures_caisse);
    $num_fact = 0;
    
    foreach ($facture->_montant_factures_caisse as $cle_facture => $montant_facture) {
      if ($acompte < $facture->_montant_avec_remise) {
        //Création de la page de la facture
        $pdf->AddPage();
        $colonne1 = 10;
        $colonne2 = 120;
        
        $font = "vera";
        
        $font = "verab";
        $pdf->setFont($font, '', 12);
        $pdf->WriteHTML("<h4>Facture du patient</h4>");
        
        $font = "vera";
        $pdf->setFont($font, '', 6);
        $pdf->setXY($colonne1, 17);  
        $pdf->Write("", "Cette page est pour vos archives");
        $pdf->setXY($colonne1, $pdf->GetY()+4);
        $pdf->Write("", "Veuillez envoyer le justificatif de remboursement");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Write("", "annexé à votre caisse maladie ou à l'assurance");
        
        $pdf->setFont($font, '', 8);
        
        //Auteur de la facture
        $auteur = array(
          "50" => "Auteur facture",
          $user->_view,
          "$group->_view",
          substr($group->adresse, 0, 30),
          substr($group->adresse, 30),
          $group->cp." ".$group->ville,
          "80" => "Four. de prestations",
          "Dr. ".$praticien->_view,
          "$group->_view",
          substr($group->adresse, 0, 30),
          substr($group->adresse, 30),
          $group->cp." ".$group->ville,
        );
        $tab[$colonne1] = $auteur;
        
        $nom_dest = "";
        if ($facture->_ref_patient->assure_nom) {
          $nom_dest =  $facture->_ref_patient->_assure_civilite." ".$facture->_ref_patient->assure_nom." ".$facture->_ref_patient->assure_prenom;
        }
        else {
          $nom_dest = $facture->_ref_patient->_view; 
        }
        
        $destinataire = array(
           "nom"=> "$nom_dest",
           "adresse"=> $facture->_ref_patient->adresse,
           "cp"=> $facture->_ref_patient->cp." ".$facture->_ref_patient->ville,
        );
         
        if ($facture->cession_creance) {
          $facture->_ref_patient->loadRefsCorrespondantsPatient();
          foreach ($facture->_ref_patient->_ref_correspondants_patient as $correspondant) {
            if ($correspondant->relation == "assurance") {
              $destinataire["nom"] = $correspondant->nom." ".$correspondant->prenom;
              $destinataire["adresse"] = $correspondant->adresse;
              $destinataire["cp"] = $correspondant->cp." ".$correspondant->ville;
            }
          }
        }
        
        
        //Destinataire de la facture
        $patient = array(
          "50" => "Destinataire",
          $destinataire["nom"],
          $destinataire["adresse"],
          $destinataire["cp"],
          "80" => "Patient",
          "n° AVS: ".$facture->_ref_patient->matricule, 
          $facture->_ref_patient->_view,
          $facture->_ref_patient->adresse,
          $facture->_ref_patient->cp." ".$facture->_ref_patient->ville
        );
        
        $tab[$colonne2] = $patient;
        if ($facture->_reglements_total_patient) {
          $pdf->SetTextColor(80,80,80);
          $pdf->setFont($font, '', 25);
          $pdf->setXY(100,20);
          $pdf->Write("", "DUPLICATA");
          $pdf->SetTextColor(0,0,0);
          $pdf->setFont($font, '', 8);
        }
        if ($facture->type_facture == "accident") {
          $pdf->SetTextColor(80,80,80);
          $pdf->setFont($font, '', 15);
          $pdf->setXY(80,40);
          $pdf->Write("", "Accident");
          $pdf->SetTextColor(0,0,0);
          $pdf->setFont($font, '', 8);
        }
        if ($facture->cession_creance) {
          $pdf->SetTextColor(80,80,80);
          $pdf->setFont($font, '', 15);
          $pdf->setXY(80,30);
          $pdf->Write("", "Cession de créance");
          $pdf->SetTextColor(0,0,0);
          $pdf->setFont($font, '', 8);
        }
        
        $x = $y = 0;
        foreach ($tab as $k => $v) {
          $colonne = $k;
          foreach ($v as $key => $value) {
            if ($key == "50" || $key == "80" ) {
              $y = $key;
              $x=0;
            }
            $pdf->setXY($colonne, $y+$x);
            $pdf->Cell(30, "", $value);
            if ($key == "50" || $key == "80") {
              $x+=5;
            }
            else {
              $x +=3;
            }
          }
        }
        
        //Données de la facture
        $pdf->SetDrawColor(0);
        $pdf->Line($colonne1, 122, $colonne1+40, 122);
        $pdf->setXY($colonne1, 120);
        $pdf->Cell(25, "", "Données de la facture", null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+5);
        $pdf->Cell(22, "", "Date facture:", null, null, "R");
        $pdf->Cell(25, "", mbTransformTime(null, null, "%d %B %Y"), null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Cell(22, "", "N° facture:", null, null, "R");
        $pdf->Cell(25, "", $facture->_id, null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Cell(22, "", "Traitement du:", null, null, "R");
        $pdf->Cell(25, "", mbTransformTime(null, $facture->ouverture, "%d %B %Y"), null, null, "L");
        $pdf->setXY($colonne1, $pdf->GetY()+3);
        $pdf->Cell(22, "", "au:", null, null, "R");
        $pdf->Cell(25, "", mbTransformTime(null, $facture->cloture, "%d %B %Y"), null, null, "L");
  
        $montant_facture = sprintf('%0.2f', $montant_facture);
        if ($montant_facture < 0) {
          $montant_facture = sprintf('%0.2f', 0);
        }
        
        //Tarif
        $title_montant = "";
        if ($nb_factures>1) {
          $num_fact++;
          $title_montant = "n° ".$num_fact;
        }
        
        $tarif = array( "Tarif"         => "CHF");
        foreach ($pre_tab as $cles => $valeur) {
          $tarif[$cles] = $valeur;
        }
        $tarif["Remise:"]         = sprintf('%0.2f', -$facture->remise);
        $tarif["Montant total:"]  = sprintf('%0.2f', $facture->_montant_avec_remise);
        $tarif["Acompte:"]        = sprintf('%0.2f', $acompte);
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
        $bvr = $genre.$montant.$cle.">".$facture->_num_reference."+ ".$adherent2.">";
          
        // Dimensions du bvr
        $largeur_bvr = 210;
        $hauteur_bvr = 106;
        $haut_doc = 297-106;
        //Une ligne <=> 4,24 mm
        $h_ligne = 106/25;
        //Une colonne <=> 2,53mm
        $l_colonne = 210/83;
        
        //Police par Défault du BVR
        $font = "vera";
        
        //Boucle utilisée pour dupliquer les Partie1 et 2 avec un décalage de colonnes
        for ($i = 0; $i<=1; $i++) {
          $decalage = $i*24*$l_colonne;
          
          //Adresse du patient
          $pdf->SetTextColor(0);
          $pdf->setFont($font, '', 8);
          $pdf->Text($l_colonne + $decalage, $h_ligne*3+$haut_doc , $praticien->_view);
          $pdf->Text($l_colonne + $decalage, $h_ligne*4+$haut_doc , $group->_view);
          //Si le texte dépasse la largeur de la colonne => retour à la ligne
          $longeur = strlen($group->adresse);
          for ($j=0; $j < $longeur/30; $j++) {
            $report = substr($group->adresse, 0+30*$j, 30);
            $pdf->Text($l_colonne + $decalage, $h_ligne*(5+$j)+$haut_doc , $report);
          }		
          $pdf->Text($l_colonne + $decalage, $h_ligne*(5+$j)+$haut_doc , $group->ville);
          
          $pdf->Text(16.75*$l_colonne + $decalage, $h_ligne*13.25+$haut_doc   , ".");			  
          //Numéro adhérent, CHF, Montant1 et Montant2
          $pdf->Text($l_colonne*11 + $decalage, $h_ligne*10.75+$haut_doc , $adherent);
          $pdf->Text($l_colonne + $decalage, $h_ligne*11.5+$haut_doc , "CHF");
            
          $pdf->setFont($font, '', 10);
          $pdf->Text($l_colonne*(17-strlen($montant_facture*100)) + $decalage, $h_ligne*13+$haut_doc , sprintf("%d", $montant_facture));
          
          $cents = floor(($montant_facture - sprintf("%d", $montant_facture))*100);
          if ($cents<10) {			
            $cents = "0".$cents;
          }
          $pdf->Text($l_colonne*19 + $decalage, $h_ligne*13+$haut_doc , $cents);
        }
        
        $pdf->Text(28*$l_colonne, $h_ligne*18+$haut_doc , "609");
        
        //écriture de la référence
        $pdf->setFont($font, '', 11);
        $pdf->Text(50*$l_colonne, $h_ligne*8.75+$haut_doc , $facture->_num_reference);
        
        $pdf->setFont($font, '', 6);
        $pdf->Text(13*$l_colonne, $h_ligne*21+$haut_doc , "Die Annahmestelle");
        $pdf->Text(13*$l_colonne, $h_ligne*21.5+$haut_doc , "L'office de dépôt");
        $pdf->Text(13*$l_colonne, $h_ligne*22+$haut_doc , "L'ufficio d'accettazione");
        
        $pdf->setFont($font, '', 8);
        $pdf->Text($l_colonne, $h_ligne*15+$haut_doc , $facture->_num_reference);
        //Adresse du patient de la facture
        $pdf->Text($l_colonne, $h_ligne*16+$haut_doc , $destinataire["nom"]);
        $pdf->Text(49*$l_colonne, $h_ligne*12+$haut_doc , $destinataire["nom"]);
        
        $longeur = strlen($destinataire["adresse"]);
        //Si le texte dépasse la largeur de la colonne => retour à la ligne
        for ($j=0; $j < $longeur/30; $j++) {
          $report = substr($destinataire["adresse"], 0+30*$j, 30);
          $pdf->Text($l_colonne, $h_ligne*(17+$j)+$haut_doc , $report);
          $pdf->Text(49*$l_colonne, $h_ligne*(13+$j)+$haut_doc , $report);
        }
        $pdf->Text($l_colonne, $h_ligne*(17+$j)+$haut_doc , $destinataire["cp"]);
        $pdf->Text(49*$l_colonne, $h_ligne*(13+$j)+$haut_doc , $destinataire["cp"]);
        
        //Ecriture du code bvr généré modulo10 récursif
        $font = "ocrbb";
        $pdf->setFont($font, '', 12);
        
        $w = (80- strlen($bvr)) *$l_colonne; 
        $pdf->Text($w, $h_ligne*21+$haut_doc, $bvr);
      }
    }
    //enregistrement pour chaque facture l'ensemble des factures
    if ($factureconsult_id) {
      $pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "I");
    }
    else {
      $exchange_source = CExchangeSource::get("tarmed_export_impression_factures", "ftp", true);
      $exchange_source->init();
  
      try {
        $exchange_source->setData($pdf->Output($facture->cloture."_".$facture->_ref_patient->nom.'.pdf', "S"));
        $exchange_source->send("", $facture->cloture."_".$facture->_ref_patient->nom.'.pdf');
      } catch(CMbException $e) {
        $e->stepAjax();
      }
    }
  }
}
if ($edition_justificatif) {
  include "justificatif.php" ;
}

if (!$factureconsult_id) {
  //Après l'envoi par ftp des fichiers, suppression du fichier print.lock
  $exchange_source = CExchangeSource::get("tarmed_export_impression_factures", "ftp", true);
  $exchange_source->init();
  try {
    $exchange_source->delFile("print.lock");
  } catch(CMbException $e) {
    $e->stepAjax();
  }
}
?>