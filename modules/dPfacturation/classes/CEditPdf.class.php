<?php
/**
 * $Id: CFacturable.class.php 17904 2013-01-30 09:17:44Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 17904 $
 */

/**
 * Classe permettant de créer des factures et justificatifs au format pdf
 */
class CEditPdf{
  //Elements du PDF
  public $type_pdf;
  public $pdf;
  public $facture;
  public $factures;
  public $relance;
  public $font;
  public $fontb;
  public $size;
  
  //Elements de la facture
  public $adresse_prat;
  public $acompte;
  public $adherent;
  public $autre_tarmed = 0;
  public $destinataire;
  public $auteur;
  public $fourn_presta;
  public $function_prat;
  public $group;
  public $group_adrr;
  public $nb_factures;
  public $num_fact;
  public $patient_facture;
  public $praticien;
  public $pre_tab      = array();
  public $type_rbt;
  
  //Elements pour le justificatif
  public $colonnes = array(20, 28, 25, 75, 30);
  
  /**
   * Fontion qui traite une adresse dans le cas d'un retour à la ligne
   * 
   * @param object $adresse l'adresse a traiter
   * 
   * @return array
   */
  function traitements($adresse) {
    $tab = array("group1" => "", "group2" => "");
    
    if (stristr($adresse, "\r\n")) {
      $tab["group1"] = stristr($adresse, "\r\n", true);
      $tab["group2"] = stristr($adresse, "\r\n");
      $tab["group2"] = str_replace("\r\n", '', $tab["group2"]);
    }
    else {
      $tab["group1"] = substr($adresse, 0, 30);
      $tab["group2"] = substr($adresse, 30);
    }
    return $tab;
  }
  
  function editCell($x, $y, $largeur, $text, $align = "") {
    $this->pdf->setXY($x, $y);
    $this->pdf->Cell($largeur, null, $text, null, null, $align);
  }

  /**
   * Edition de la facture
   *
   * @param bool $ts tiers soldant
   *
   * @return void
   */
  function editFactureBVR($ts = false) {
    $this->type_pdf = $ts ? "BVR_TS" : "BVR"; 
    $this->editFacture();
    //enregistrement pour chaque facture l'ensemble des factures
    if (count($this->factures)) {
      $this->pdf->Output($this->facture->cloture."_".$this->patient_facture->nom.'.pdf', "I");
    }
    else {
      $this->pdf->Output('Factures.pdf', "I");
    }
  }

  /**
   * Edition du justifiactif
   *
   * @param bool $ts tiers soldant
   *
   * @return void
   */
  function editJustificatif($ts = false) {
    $this->type_pdf = $ts ? "justif_TS" : "justif"; 
    $this->editFacture();
    if (count($this->factures)) {
      $this->pdf->Output($this->facture->cloture."_".$this->patient_facture->nom.'.pdf', "I");
    }
    else {
      $this->pdf->Output('Justificatifs.pdf', "I");
    }
  }
  
  /**
   * Edition de la relance
   *
   * @return void
   */
  function editRelance() {
    $this->type_pdf = "relance";
    $this->editFacture();
    if (count($this->factures)) {
      $this->pdf->Output("Relance_".$this->facture->cloture."_".$this->patient_facture->nom.'.pdf', "I");
    }
    else {
      $this->pdf->Output('Relances.pdf', "I");
    }
  }
  
  /**
   * Edition de la facture
   *
   * @return void
   */
  function editFacture() {
    // Creation du PDF
    $this->pdf = new CMbPdf('P', 'mm');
    $this->pdf->setPrintHeader(false);
    $this->pdf->setPrintFooter(false);
    $this->font = "vera";
    $this->fontb = $this->font."b";
    
    foreach ($this->factures as $the_facture) {
      $this->facture = $the_facture;
      
      $this->patient_facture = $this->facture->loadRefPatient();
      $this->facture->_ref_patient->loadRefsCorrespondantsPatient();
      $this->praticien = $this->facture->loadRefPraticien();
      $this->facture->loadRefAssurance();
      $this->facture->loadRefsObjects();
      $this->facture->loadRefsReglements();
      if ($this->type_pdf == "relance") {
        $this->facture->loadRefsRelances();
      }
  
      $this->function_prat = $this->praticien->loadRefFunction();
      $this->group = $this->function_prat->loadRefGroup();
      $this->adherent = $this->praticien->adherent;
      
      if ($this->type_pdf == "BVR") {
        $this->loadTotaux();
        $this->acompte = 0;
        $this->nb_factures = count($this->facture->_montant_factures_caisse);
        $this->num_fact = 0;
        foreach ($this->facture->_montant_factures_caisse as $cle_facture => $montant_facture) {
          if ($this->acompte < $this->facture->_montant_avec_remise) {
            $this->editHautFacture($cle_facture, $montant_facture);
            $this->editBVR($montant_facture);
          }
        }
      }
      if ($this->type_pdf == "BVR_TS") {
        $this->loadTotaux();
        $this->acompte = 0;
        $this->nb_factures = count($this->facture->_montant_factures_caisse);
        $this->num_fact = 0;
        $montant = 0;
        if ($this->acompte < $this->facture->_montant_avec_remise) {
          $montant = $this->facture->_montant_avec_remise - $this->facture->_reglements_total_patient - $this->facture->_reglements_total_tiers;
          $this->editHautFacture(" ", $montant);
          $this->editBVR($montant);
        }
        $this->type_pdf = "justif_TS";
        $this->function_prat->adresse = str_replace("\r\n", ' ', $this->function_prat->adresse);
        $this->patient_facture->adresse = str_replace("\r\n", ' ', $this->patient_facture->adresse);
        $this->editCenterJustificatif(0, $montant);
      }
      elseif ($this->type_pdf == "justif") {
        $this->function_prat->adresse = str_replace("\r\n", ' ', $this->function_prat->adresse);
        $this->patient_facture->adresse = str_replace("\r\n", ' ', $this->patient_facture->adresse);
        
        foreach ($this->facture->_montant_factures_caisse as $cle_facture => $montant_facture) {
          $this->editCenterJustificatif($cle_facture, $montant_facture);
        }
      }
      elseif ($this->type_pdf == "relance") {
        $this->editHautFacture(1, $this->relance->_montant, true);
        $this->editBVR($this->relance->_montant);
      }
    }
  }
  
  /**
   * Edition du centre du justificatif
   *
   * @param num $cle_facture     clé de la facture
   * @param num $montant_facture montant de la facture
   *
   * @return void
   */
  function editCenterJustificatif($cle_facture, $montant_facture) {
    $this->loadAllElements();
    $this->pdf->AddPage();
    $pm = $pt = 0;
    
    $this->ajoutEntete1();
    $this->pdf->setFont($this->font, '', 8);
    $tailles_colonnes = array(
      "Date" => 9,      "Tarif"=> 5,
      "Code" => 7,      "Code réf" => 7,
      "Sé Cô" => 5,     "Quantité" => 9,
      "Pt PM/Prix" => 8,"fPM" => 5,
      "VPtPM" => 6,     "Pt PT" => 7,
      "fPT" => 5,       "VPtPT" => 5,
      "E" => 2,         "R" => 2,
      "P" => 2,         "M" => 2,
      "Montant" => 10 );
    
    $x = 0;
    $this->pdf->setX(10);
    foreach ($tailles_colonnes as $key => $value) {
      $this->editCell($this->pdf->getX()+$x, 140, $value, $key, "C");
      $x = $value;
    }
    $ligne = 0;
    $debut_lignes = 140;
    $nb_pages = 1;
    $montant_intermediaire = 0;
    $tab_actes = array (
      "tarmed" => $this->facture->_ref_actes_tarmed,
      "caisse" => $this->facture->_ref_actes_caisse
    );
    foreach ($tab_actes as $keytab => $tab_acte) {
      foreach ($tab_acte as $acte) {
        if ( (($cle_facture == 0 && $keytab == "tarmed") ||
            ($keytab == "caisse" && ($cle_facture == 1 || ($acte->_class == "CActeCaisse" && $acte->_ref_caisse_maladie->use_tarmed_bill) || 
            ( $acte->_class == "CFactureItem" && $acte->use_tarmed_bill))))
            && (($acte->quantite != 0 && CAppUI::conf("dPfacturation Other use_view_quantitynull")) || !CAppUI::conf("dPfacturation Other use_view_quantitynull"))) {
          $ligne++;
          $this->pdf->setXY(37, $debut_lignes + $ligne*3);
          //Traitement pour le bas de la page et début de la suivante
          if ($this->pdf->getY()>=265) {
            $this->pdf->setFont($this->fontb, '', 8);
            $this->pdf->editCell($this->pdf->getX(), $debut_lignes + $ligne*3, 130, "Total Intermédiaire", "R");
            $this->pdf->Cell(28, "", $montant_intermediaire , null, null, "R");
            $this->pdf->setFont($this->font, '', 8);
            $this->pdf->AddPage();
            $nb_pages++;
            $this->ajoutEntete2($nb_pages);
            $this->pdf->editCell(10, $this->pdf->getY()+4, $colonnes[0]+$colonnes[1], "Patient");
            $this->pdf->Cell($colonnes[2], "", $this->patient_facture->nom." ".$this->patient_facture->prenom." ".$this->patient_facture->naissance);
            $this->pdf->Line(10, 42, 190, 42);
            $this->pdf->Line(10, 38, 10, 42);
            $this->pdf->Line(190, 38, 190, 42);
            $ligne = 0;
            $debut_lignes = 50;
            $this->pdf->setXY(10, 0);
          }
          $this->pdf->setFont($this->fontb, '', 7);
          $this->pdf->setXY(37, $debut_lignes + $ligne*3);
         
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
            $coeff_fact = $this->facture->_coeff;
          }
          elseif ($acte->_class == "CActeCaisse") {
            $libelle = $acte->_ref_prestation_caisse->libelle;
            $nom_coeff = "coeff_".$this->facture->type_facture;
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
          
          $this->pdf->Write("<b>", substr($libelle, 0, 90));
          $ligne++;
          //Si le libelle est trop long
          if (strlen($libelle)>90) {
            $this->pdf->setXY(37, $debut_lignes + $ligne*3);
            $this->pdf->Write("<b>", substr($libelle, 90));
            $ligne++;
          }
          $x = 0;
          $this->pdf->setX(10);
          $this->pdf->setFont($this->font, '', 8);
          foreach ($tailles_colonnes as $key => $largeur) {
            $valeur = "";
            $cote = "C";
            switch ($key) {
              case "Date" :
                $valeur = $acte->date;
                $valeur= CMbDT::transform(null, $valeur, "%d.%m.%Y");
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
                $this->pdf->setX($this->pdf->getX()+3);
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
            $this->editCell($this->pdf->getX()+$x, $debut_lignes + $ligne*3, $largeur, $valeur, $cote);
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
    
    $this->pdf->setFont($this->fontb, '', 8);
    $ligne = 265;
    $l = 20;
    $this->editCell(20, $ligne+3, $l, "Tarmed PM", "R");
    $this->pdf->Cell($l, "", $pm, null, null, "R");
    
    $this->editCell(20, $ligne+6, $l, "", "Tarmed PT", "R");
    $this->pdf->Cell($l, "", $pt, null, null, "R");
        
    $autre_temp = $cle_facture == 0 ? $montant_facture - $pm - $pt : $montant_facture;
    $autre_temp = round("%.2f", $autre_temp);
    $autre = ($autre_temp <= 0.05) ? 0.00 : $autre_temp;
    $this->editCell(70, $ligne+3, $l, "Autres", "R");
    $this->pdf->Cell($l, "",  sprintf("%.2f", $autre), null, null, "R");
    
    $this->editCell(20, $ligne+9, $l, "Montant total/CHF", "R");
    $this->pdf->Cell($l, "", sprintf("%.2f", $montant_intermediaire), null, null, "R");
    
    $acompte = sprintf("%.2f", $this->facture->_reglements_total_patient);
    $this->pdf->Cell(30, "", "Acompte", null, null, "R");
    $this->pdf->Cell($l, "", "".$acompte, null, null, "R");
    $this->pdf->Cell($l, "", "", null, null, "R");
    
    $total_temp = $montant_intermediaire - $this->facture->_reglements_total_patient;
    $total = $total_temp<0 ? 0.00 : $total_temp;
    
    $this->pdf->Cell($l, "", "Montant dû", null, null, "R");
    $this->pdf->Cell($l, "", sprintf("%.2f", $total), null, null, "R");
  }
  
  /**
   * Calcul des totaux
   * 
   * @return void
   */
  function loadTotaux(){
    $pm = 0;
    $pt = 0;
    foreach ($this->facture->_ref_actes_tarmed as $acte) {
      if ($acte->_class == "CActeTarmed") {
        if ($acte->_ref_tarmed->tp_al == 0.00 && $acte->_ref_tarmed->tp_tl == 0.00) {
          if ($acte->code_ref && (preg_match("Reduction", $acte->libelle) || preg_match("Majoration", $acte->libelle))) {
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
    
    foreach ($this->facture->_ref_actes_caisse as $acte) {
      if ($acte->_class == "CActeCaisse") {
        if ($acte->_ref_caisse_maladie->use_tarmed_bill) {
          $this->autre_tarmed += $acte->montant_base;
        }
      }
      elseif ($acte->use_tarmed_bill) {
        $this->autre_tarmed += $acte->montant_base;
      }
    }
    $pt = sprintf("%.2f", $pt * $this->facture->_coeff);
    $pm = sprintf("%.2f", $pm * $this->facture->_coeff);
    
    $this->pre_tab["Medical:"]  = $pm;
    $this->pre_tab["Tarmed:"]   = $pt;
    $this->pre_tab["Autres:"]   = sprintf("%.2f", $this->facture->_montant_sans_remise - $pm - $pt - $this->autre_tarmed);
  }
  
  /**
   * Edition du haut de la facture
   *
   * @param num  $cle_facture     clé de la facture
   * @param num  $montant_facture montant de la facture
   * @param bool $relance         si c'est une relance
   *
   * @return void
   */
  function editHautFacture($cle_facture, $montant_facture, $relance = false) {
    $this->loadAllElements();
    //Création de la page de la facture
    $this->pdf->AddPage();
    $colonne1 = 10;
    $colonne2 = 120;
    
    $this->pdf->setFont($this->fontb, '', 12);
    $this->pdf->WriteHTML("<h4>Facture du patient</h4>");
    
    $this->pdf->setFont($this->font, '', 6);
    $this->pdf->Text($colonne1, 17, "Cette page est pour vos archives");
    $this->pdf->Text($colonne1, 21, "Veuillez envoyer le justificatif de remboursement");
    $this->pdf->Text($colonne1, 24, "annexé à votre caisse maladie ou à l'assurance");
    
    $this->pdf->setFont($this->font, '', 8);
    
    $auteur = array(
      "50" => "Auteur facture",
      $this->auteur["nom"],
      $this->auteur["adresse1"],
      $this->auteur["adresse2"],
      $this->auteur["cp"]." ".$this->auteur["ville"],
      "80" => "Four. de prestations",
      $this->fourn_presta["nom_dr"],
      $this->fourn_presta["fct"],
      $this->fourn_presta["adresse1"],
      $this->fourn_presta["adresse2"],
      $this->fourn_presta["0"]->cp." ".$this->fourn_presta["0"]->ville
    );
    $tab[$colonne1] = $auteur;

    $patient_adrr = $this->traitements($this->patient_facture->adresse);
    //Destinataire de la facture
    $patient = array(
      "50" => "Destinataire",
      $this->destinataire["nom"],
      $this->destinataire["adresse1"],
      $this->destinataire["adresse2"],
      $this->destinataire["cp"],
      "80" => "Patient",
      "n° AVS: ".$this->patient_facture->avs,
      $this->patient_facture->_view,
      $patient_adrr["group1"],
      $patient_adrr["group2"],
      $this->patient_facture->cp." ".$this->patient_facture->ville
    );
    
    $tab[$colonne2] = $patient;
    $this->pdf->SetTextColor(80, 80, 80);
  
    if ($relance) {
      $this->pdf->setFont($this->font, '', 25);
      $this->pdf->Text(100, 20, "RELANCE");
    }
    /*
    elseif ($this->facture->_reglements_total_patient) {
      $this->pdf->setFont($this->font, '', 25);
      $this->pdf->Text(100,20, "DUPLICATA");
    }
    if ($this->facture->type_facture == "accident") {
      $this->pdf->setFont($this->font, '', 15);
      $this->pdf->Text(80,40, "Accident");
    }
    if ($this->facture->cession_creance) {
      $this->pdf->setFont($this->font, '', 15);
      $this->pdf->Text(80,30, "Cession de créance");
    }
    */
    $this->pdf->SetTextColor(0, 0, 0);
    $this->pdf->setFont($this->font, '', 8);
    
    // Ecriture de C, D, E, F
    $x = $y = 0;
    foreach ($tab as $k => $v) {
      foreach ($v as $key => $value) {
        if ($value) {
          if ($key == "50" || $key == "80" ) {
            $y = $key;
            $x=0;
          }
          $this->editCell($k, $y+$x, 30, $value);
          $x = ($key == "50" || $key == "80") ? $x+5 : $x+3;
        }
      }
    }
    
    // G : Données de la facture
    $this->pdf->SetDrawColor(0);
    $this->pdf->Line($colonne1, 122, $colonne1+40, 122);
    $this->editCell($colonne1, 120, 25, "Données de la facture", "L");
    $this->editCell($colonne1, $this->pdf->GetY()+5, 22, "Date facture:", "R");
    $this->pdf->Cell(25, "", CMbDT::transform(null, $this->facture->cloture, "%d %B %Y"), null, null, "L");
    if ($relance) {
      $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "Date relance:", "R");
      $this->pdf->Cell(25, "", CMbDT::transform(null, $this->relance->date, "%d %B %Y"), null, null, "L");
    }
    $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "N° facture:", "R");
    $this->pdf->Cell(25, "", $this->facture->_id, null, null, "L");
    $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "Traitement du:", "R");
    $this->pdf->Cell(25, "", CMbDT::transform(null, $this->facture->_ref_first_consult->_date, "%d %B %Y"), null, null, "L");
    $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "au:", "R");
    $this->pdf->Cell(25, "", CMbDT::transform(null, $this->facture->cloture, "%d %B %Y"), null, null, "L");
    
    $montant_facture = sprintf('%0.2f', $montant_facture);
    if ($montant_facture < 0) {
      $montant_facture = sprintf('%0.2f', 0);
    }
    
    // H : Tarif
    $title_montant = "";
    if ($this->nb_factures>1) {
      $this->num_fact++;
      $title_montant = "n° ".$this->num_fact;
    }
    
    $montant_total = 0;
    $tarif = array( "Tarif"         => "CHF");
    $acompte = $this->type_pdf == "BVR_TS" ? $this->facture->_montant_avec_remise - $montant_facture : "0.00";
    foreach ($this->pre_tab as $cles => $valeur) {
      if ($this->type_pdf == "BVR_TS") {
        $tarif[$cles] = $valeur;
        $montant_total += $valeur;
      }
      elseif (($cle_facture == 0 && $cles != "Autres:") || ($cle_facture == 1 && $cles == "Autres:")) {
        $tarif[$cles] = $valeur;
        $montant_total += $valeur;
      }
      elseif ($cle_facture == 0) {
        $tarif[$cles] = sprintf('%0.2f', $this->autre_tarmed);
        $montant_total += sprintf('%0.2f', $this->autre_tarmed);
      }
      else {
        $tarif[$cles] = "0.00";
      }
    }
    if ($relance) {
      $tarif["Relance:"]      = sprintf('%0.2f', $this->relance->_montant);
    }
    $tarif["Remise:"]         = sprintf('%0.2f', -$this->facture->remise);
    $tarif["Montant total:"]  = sprintf('%0.2f', $montant_total);
    $tarif["Acompte:"]        = sprintf('%0.2f', $acompte);
    $tarif["Montant dû $title_montant:"]  = $montant_facture;
    
    $this->acompte += $montant_facture;
    $this->pdf->Line($colonne2, 122, $colonne2+50, 122);
    
    $x = 0;
    foreach ($tarif as $key => $value) {
      $this->editCell($colonne2, 120+$x, 25, $key, "R");
      $this->pdf->Cell(22, "", $value, null, null, "R");
      
      if ($key == "Tarif" || $key == "Remise:") {
        $x+=5;
        if ($key == "Remise:") {
          $this->pdf->Line($colonne2, 117 +$x, $colonne2+50, 117 +$x);
          $this->pdf->setFont($this->fontb, '', 8);
        }
      }
      else {
        $x+=3;
      }
    } 
  }
  
  /**
   * Edition du bas de la facture (partie BVR)
   *
   * @param num $montant_facture montant du BVR
   *
   * @return void
   */
  function editBVR($montant_facture) {
    //le 01 sera fixe car il correspond à un "Codes des genres de justificatifs (BC)" ici :01 = BVR en CHF
    $genre = "01";
    $montant = sprintf('%010d', $montant_facture*100);
    $cle = $this->facture->getNoControle($genre.$montant);
    $this->adherent2 = str_replace(' ', '', $this->praticien->adherent);
    $this->adherent2 = str_replace('-', '', $this->adherent2);
    $_num_reference = str_replace(' ', '', $this->facture->num_reference);
    $bvr = $genre.$montant.$cle.">".$_num_reference."+ ".$this->adherent2.">";
    
    // Dimensions du bvr
    $largeur_bvr = 210;
    $hauteur_bvr = 106;
    $haut_doc = 297-$hauteur_bvr;
    
    // Une ligne = 1/6 pouce = 4.2333 mm
    $h_ligne = 4.2333; // $hauteur_bvr/25;
    
    // Une colonne = 1/10 pouce = 2.54 mm
    $l_colonne = 2.54; // $largeur_bvr/83;
    
    $left_offset = 84 * $l_colonne - $largeur_bvr;
    
    //Boucle utilisée pour dupliquer les Partie1 et 2 avec un décalage de colonnes
    for ($i = 0; $i<=1; $i++) {
      $decalage = $i*24*$l_colonne + $left_offset;
      
      //Adresse du patient
      $this->pdf->SetTextColor(0);
      $this->pdf->setFont($this->font, '', 8);
      $this->auteur["nom"] = CAppUI::conf("dPfacturation CEditPdf home_nom");
      
      if (!$this->fourn_presta["fct"]) {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*4+$haut_doc , $this->auteur["nom"]);
      }
      else {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*3+$haut_doc , $this->auteur["nom"]);
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*4+$haut_doc , $this->fourn_presta["fct"]);
      }
      
      $j = 1;
      $this->pdf->Text($l_colonne + $decalage, $h_ligne*5+$haut_doc , $this->auteur["adresse1"]);
      if ($this->auteur["adresse2"]) {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*6+$haut_doc , $this->auteur["adresse2"]);
        $j = 2;
      }
      $this->pdf->Text($l_colonne + $decalage, $h_ligne*(5+$j)+$haut_doc , $this->auteur["cp"]." ".$this->auteur["ville"]);
      
      //Numéro adhérent, CHF, Montant1 et Montant2
      $this->pdf->Text($l_colonne*11 + $decalage, $h_ligne*10.75+$haut_doc , $this->adherent);
      
      $this->pdf->setFont($this->font, '', 10);
      $this->pdf->Text($l_colonne*(17-strlen($montant_facture*100)) + $decalage, $h_ligne*13+$haut_doc , sprintf("%d", $montant_facture));
      
      $cents = floor(sprintf("%.2f", $montant_facture - sprintf("%d", $montant_facture))*100);
      if ($cents<10) {
        $cents = "0".$cents;
      }
      $this->pdf->Text($l_colonne*19 + $decalage, $h_ligne*13+$haut_doc , $cents);
    }
    $decalage = $left_offset; // 7.36 // 8;
    
    //Ecriture de la reference
    $num_reference = preg_replace("/^(\d{2})(\d{5})(\d{5})(\d{5})(\d{5})$/", '\\1 \\2 \\3 \\4 \\5 \\6', $this->facture->num_reference);
    $this->pdf->setFont($this->font, '', 11);
    $this->pdf->Text(50*$l_colonne, $h_ligne*8.75+$haut_doc , $num_reference);
    
    $this->pdf->setFont($this->font, '', 8);
    $this->pdf->Text($l_colonne + $decalage, $h_ligne*15+$haut_doc , $this->facture->num_reference);
    //Adresse du patient de la facture
    $this->pdf->Text($l_colonne + $decalage, $h_ligne*16+$haut_doc , $this->destinataire["nom"]);
    $this->pdf->Text(49*$l_colonne + $decalage, $h_ligne*12+$haut_doc , $this->destinataire["nom"]);
    
    $this->pdf->Text($l_colonne + $decalage, $h_ligne*17+$haut_doc , $this->destinataire["adresse1"]);
    $this->pdf->Text(49*$l_colonne + $decalage, $h_ligne*13+$haut_doc , $this->destinataire["adresse1"]);
    $j = 1;
    if ($this->destinataire["adresse2"]) {
      $this->pdf->Text($l_colonne + $decalage, $h_ligne*(18)+$haut_doc , $this->destinataire["adresse2"]);
      $this->pdf->Text(49*$l_colonne + $decalage, $h_ligne*14+$haut_doc , $this->destinataire["adresse2"]);
      $j = 2;
    }
    
    $this->pdf->Text($l_colonne + $decalage, $h_ligne*(17+$j)+$haut_doc , $this->destinataire["cp"]);
    $this->pdf->Text(49*$l_colonne + $decalage, $h_ligne*(13+$j)+$haut_doc , $this->destinataire["cp"]);
    
    //Ecriture du code bvr genere modulo10 recursif
    $this->pdf->setFont("ocrbb", '', 12);
    
    $w = (80- strlen($bvr)) *$l_colonne - $decalage;
    $this->pdf->Text($w, $h_ligne*21+$haut_doc, $bvr);
  }

  /**
   * Création du premier type d'en-tête possible d'un justificatif 
   * 
   * @return void
   */
  function ajoutEntete1(){
    $this->ajoutEntete2(1);
    $this->pdf->SetFillColor(255, 255, 255);
    $this->pdf->SetDrawColor(0);
    $this->pdf->Rect(10, 38, 180, 100);
    
    $_ref_assurance = "";
    $nom_entreprise = "";
    if ($this->facture->type_facture == "accident" && $this->facture->assurance_maladie && $this->facture->_ref_assurance_maladie->employeur) {
      $employeur = new CCorrespondantPatient();
      $employeur->load($this->facture->_ref_assurance_maladie->employeur);
      $_ref_assurance = $employeur->num_assure;
      $nom_entreprise = $employeur->nom;
    }
    
    $loi = $this->facture->type_facture == "accident" ? "LAA" : "LAMal";
    if ($this->facture->type_facture == "accident" && $this->facture->_coeff == CAppUI::conf("tarmed CCodeTarmed pt_maladie")) {
      $loi = "LAMal";
    }
    if ($this->facture->statut_pro == "invalide") {
      $loi = "LAI";
    }
    
    $assurance_patient = $this->destinataire[0];
    $assur_nom = "";
    if ($this->facture->_class != "CFactureCabinet" && $this->facture->dialyse && $this->facture->_ref_assurance_accident) {
      $assur_nom = $this->facture->_ref_assurance_accident->nom." ".$this->facture->_ref_assurance_accident->prenom;
    }
    if (isset($assurance_patient->type_pec) && $assurance_patient->type_pec == "TS" && $this->type_rbt == "TG avec cession") {
      if (count($this->facture->_ref_reglements) && $this->type_pdf == "justif_TS") {
        $assur_nom = $this->patient_facture->nom." ".$this->patient_facture->prenom;
      }
      else {
        $assur_nom = "$assurance_patient->nom $assurance_patient->prenom";
      }
      $assurance_patient = $this->patient_facture;
    }
    
    $assur = array();
    $assur["civilite"]  = isset($assurance_patient->civilite) ? ucfirst($this->patient_facture->civilite) : "";
    $assur["nom"]     = "$assurance_patient->nom $assurance_patient->prenom";
    $assur["adresse"] = "$assurance_patient->adresse";
    $assur["cp"]      = "$assurance_patient->cp $assurance_patient->ville";
    
    $motif = $this->facture->type_facture;
    if ($this->facture->type_facture == "accident" && $this->facture->_coeff == CAppUI::conf("tarmed CCodeTarmed pt_maladie")) {
      $motif = "Accident (Caisse-Maladie)";
    }
    $naissance =  CMbDT::transform(null, $this->patient_facture->naissance, "%d.%m.%Y");
    $colonnes = array(20, 28, 25, 25, 25, 50);
    $traitement = CMbDT::transform(null, $this->facture->_ref_first_consult->_date, "%d.%m.%Y")." - ".CMbDT::transform(null, $this->facture->cloture, "%d.%m.%Y");
    $name_rappel = $date_rappel = null;
    if (CAppUI::conf("dPfacturation CRelance use_relances")) {
      $name_rappel = "Date rappel";
      $date_rappel = CMbDT::date("+".CAppUI::conf("dPfacturation CRelance nb_days_first_relance")." DAY" , $this->facture->cloture);
      $date_rappel = CMbDT::transform(null, $date_rappel, "%d.%m.%Y");
    }
   $ean2 = $this->group->ean;
    if ($this->facture->_class == "CFactureEtablissement") {
      $ean2 = $this->facture->_ref_last_sejour->_ref_last_operation->_ref_anesth->ean;
    }
    $lignes = array(
      array("Patient"   , "Nom"             , $this->patient_facture->nom     ,null, "Assurance", $assur_nom),
      array(""          , "Prénom"          , $this->patient_facture->prenom),
      array(""          , "Rue"             , $this->patient_facture->adresse),
      array(""          , "NPA"             , $this->patient_facture->cp      , null, $assur["civilite"]),
      array(""          , "Localité"        , $this->patient_facture->ville   , null, $assur["nom"]),
      array(""          , "Date de naissance",$naissance        , null, $assur["adresse"]),
      array(""          , "Sexe"            , strtoupper($this->patient_facture->sexe) , null, $assur["cp"]),
      array(""          , "Date cas"        , CMbDT::transform(null, $this->facture->cloture, "%d.%m.%Y")),
      array(""          , "N° cas"          , $this->facture->ref_accident),
      array(""          , "N° AVS"          , $this->patient_facture->avs),
      array(""          , "N° assuré"       , $_ref_assurance),
      array(""          , "Nom entreprise"  , $nom_entreprise),
      array(""          , "Canton"          , "GE"),
      array(""          , "Copie"           , "Non"),
      array(""          , "Type de remb."   , $this->type_rbt),
      array(""          , "Loi"             , $loi),
      array(""          , "N° contrat"      , ""),
      array(""          , "Motif traitement", $motif  , null, "N° facture", $this->facture->_id),
      array(""          , "Traitement"      , $traitement, null, $name_rappel, $date_rappel),
      array(""          , "Rôle/ Localité"  , "-"),
      array("Mandataire", "N° EAN/N° RCC"   , $this->praticien->ean." - ".$this->praticien->rcc, null, $this->praticien->_view),
      array("Diagnostic", "U / Toute demande d'information est à adresser au chirurgien ".$this->praticien->_view),
      array("Liste EAN" , "", "1/".$this->praticien->ean." 2/".$ean2),
      array("Commentaire")
    );
    
    foreach ($lignes as $ligne) {
      $this->pdf->setXY(10, $this->pdf->getY()+4);
      foreach ($ligne as $key => $value) {
        $this->pdf->Cell($colonnes[$key], "", $value);
      }
    }
    $this->pdf->Line(10, 119, 190, 119);
    $this->pdf->Line(10, 123, 190, 123);
    $this->pdf->Line(10, 127, 190, 127);
    $this->pdf->Line(10, 131, 190, 131);
  }
  
  /**
   * Création du second type d'en-tête possible d'un justificatif, celui-ci étant plus léger 
   * 
   * @param int $nb le numéro de la page
   * 
   * @return void
   */
  function ajoutEntete2($nb){
    $this->pdf->setFont($this->fontb, '', 12);
    $this->pdf->WriteHTML("<h4>Justificatif de remboursement</h4>");
    
    $this->pdf->setFont($this->font, '', 8);
    $this->pdf->SetFillColor(255, 255, 255);
    $this->pdf->SetDrawColor(0);
    $this->pdf->Rect(10, 18, 180, 20);
    
    $lignes = array(
      array("Document"    , "Identification"  , $this->facture->_id." ".CMbDT::transform(null, null, "%d.%m.%Y %H:%M:%S"), "", "Page $nb"),
      array("Auteur"      , "N° EAN(B)"       , $this->auteur["EAN"], $this->auteur["nom"], " Tél: ".$this->auteur["tel"]),
      array("Facture"     , "N° RCC(B)"       , $this->auteur["RCC"], substr($this->auteur["adresse1"], 0, 29)." ". $this->auteur["cp"]." ".$this->auteur["ville"], "Fax: ".$this->auteur["fax"]),
      array("Four.de"     , "N° EAN(P)"       , $this->fourn_presta["EAN"], $this->fourn_presta["nom_dr"], " Tél: ".$this->fourn_presta["0"]->tel),
      array("prestations" , "N° RCC(B)"       , $this->fourn_presta["RCC"], substr($this->fourn_presta["adresse1"], 0, 29)." ". $this->fourn_presta["0"]->cp." ".$this->fourn_presta["0"]->ville, "Fax: ".$this->fourn_presta["0"]->fax)
    );
    
    $this->pdf->setXY(10, $this->pdf->getY()-4);
    foreach ($lignes as $ligne) {
      $this->pdf->setXY(10, $this->pdf->getY()+4);
      foreach ($ligne as $key => $value) {
        $this->pdf->Cell($this->colonnes[$key], "", $value);
      }
    }
  }
  
  /**
   * Chargement de tous les éléments communs
   * 
   * @return void
   */
  function loadAllElements(){
    //Auteur de la facture
    $this->adresse_prat = $this->traitements($this->function_prat->adresse);
    $this->group_adrr = $this->traitements($this->group->adresse);
    
    if (strlen($this->function_prat->cp)>4) {
      $this->function_prat->cp =  substr($this->function_prat->cp, 1);
    }
    if (strlen($this->patient_facture->cp)>4) {
      $this->patient_facture->cp =  substr($this->patient_facture->cp, 1);
    }
    
    //Assurance
    $assur = array();
    $assurance_patient = null;
    $view = "_longview";
    $this->type_rbt = "TG";
    
    // TP uniquement pour accident 
    // TP/TG/TS      pour maladie
    if ($this->facture->assurance_maladie && !$this->facture->send_assur_base && $this->facture->_ref_assurance_maladie->type_pec != "TG" && $this->facture->type_facture == "maladie") {
      $assurance_patient = $this->facture->_ref_assurance_maladie;
      $this->type_rbt = $this->facture->_ref_assurance_maladie->type_pec;
    }
    elseif ($this->facture->assurance_accident && !$this->facture->send_assur_compl && $this->facture->type_facture == "accident") {
      if ($this->facture->_coeff == CAppUI::conf("tarmed CCodeTarmed pt_maladie")) {
        $this->type_rbt = $this->facture->_ref_assurance_accident->type_pec;
      }
      else {
        $this->type_rbt = "TP";
      }

      if ($this->type_rbt == "TG") {
        $assurance_patient = $this->patient_facture;
      }
      else {
        $assurance_patient = $this->facture->_ref_assurance_accident;
      }
    }
    else {
      $assurance_patient = $this->patient_facture;
      $view = "_view";
    }
    if (count($this->facture->_ref_reglements) && $this->type_pdf == "BVR_TS") {
      $assurance_patient = $this->patient_facture;
      $view = "_view";
    }
    
    $this->type_rbt = $this->type_rbt == "" ? "TG" : $this->type_rbt;
    $this->type_rbt = $this->type_rbt == "TS" ? "TG avec cession" : $this->type_rbt;
    
    $assur["nom"]     = $assurance_patient->$view;
    $assur["adresse"] = $assurance_patient->adresse;
    $assur["cp"]      = $assurance_patient->cp." ".$assurance_patient->ville;
    
    $assur_adrr = $this->traitements($assur["adresse"]);
    $this->destinataire = array(
      "0"         => $assurance_patient,
      "nom"       => $assur["nom"],
      "adresse1"  => $assur_adrr["group1"],
      "adresse2"  => $assur_adrr["group2"],
      "cp"        => $assur["cp"],
    );
    
    $this->auteur = array(
      "0"        =>  $this->group,
      "nom"      =>  $this->group->raison_sociale,
      "adresse1" =>  $this->group_adrr["group1"],
      "adresse2" =>  $this->group_adrr["group2"],
      "cp"       =>  $this->group->cp,
      "ville"    =>  $this->group->ville,
      "EAN"      =>  $this->group->ean,
      "RCC"      =>  $this->group->rcc,
      "tel"      =>  $this->group->tel,
      "fax"      =>  $this->group->fax,
    );
    if (!CAppUI::conf("dPfacturation CEditPdf use_bill_etab")) {
      $this->fourn_presta = array(
        "0"        =>  $this->function_prat,
        "nom_dr"   =>  "Dr. ".$this->praticien->_view,
        "fct"      =>  $this->function_prat->_view,
        "adresse1" =>  $this->adresse_prat["group1"],
        "adresse2" =>  $this->adresse_prat["group2"],
        "EAN"      =>  $this->praticien->ean,
        "RCC"      =>  $this->praticien->rcc
      );
    }
    else {
      $this->fourn_presta = $this->auteur;
      $this->fourn_presta["nom_dr"] = $this->group->raison_sociale;
      $this->fourn_presta["fct"] = "";
      $this->auteur["nom"]      = CAppUI::conf("dPfacturation CEditPdf home_nom");
      $this->auteur["adresse1"] = CAppUI::conf("dPfacturation CEditPdf home_adresse");
      $this->auteur["adresse2"] = "";
      $this->auteur["cp"]       = CAppUI::conf("dPfacturation CEditPdf home_cp");
      $this->auteur["ville"]    = CAppUI::conf("dPfacturation CEditPdf home_ville");
      $this->auteur["EAN"]      = CAppUI::conf("dPfacturation CEditPdf home_EAN");
      $this->auteur["RCC"]      = CAppUI::conf("dPfacturation CEditPdf home_RCC");
      $this->auteur["tel"]      = CAppUI::conf("dPfacturation CEditPdf home_tel");
      $this->auteur["fax"]      = CAppUI::conf("dPfacturation CEditPdf home_fax"); 
    }
  }
}