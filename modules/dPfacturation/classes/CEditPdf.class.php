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

/**
 * Classe permettant de créer des factures et justificatifs au format pdf
 */
class CEditPdf{
  //Elements du PDF
  public $type_pdf;
  /** @var CMbPdf*/
  public $pdf;
  /** @var CFactureEtablissement*/
  public $facture;
  /** @var CFactureEtablissement[]*/
  public $factures;
  /** @var CRelance*/
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
  /** @var CFunctions*/
  public $function_prat;
  /** @var CGroups*/
  public $group;
  public $group_adrr;
  public $nb_factures;
  public $num_fact;
  /** @var CPatient*/
  public $patient;
  /** @var CMediusers*/
  public $praticien;
  public $pre_tab      = array();
  public $type_rbt;
  public $adherent2;

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

  /**
   * Fontion qui permet de positionner le curseur et ecrire une cellule
   *
   * @param int    $x       position du curseur à placer en x
   * @param int    $y       position du curseur à placer en y
   * @param int    $largeur largeur de la cellule
   * @param string $text    text de la cellule
   * @param string $align   alignement à gauche par défault
   * @param string $border  bordure
   * @param int    $hauteur hauteur
   *
   * @return void
   */
  function editCell($x, $y, $largeur, $text, $align = "", $border = "", $hauteur = "") {
    $this->pdf->setXY($x, $y);
    $this->pdf->Cell($largeur, $hauteur, $text, $border, null, $align);
  }

  /**
   * Edition de la facture
   *
   * @param bool   $ts     tiers soldant
   * @param string $stream format de sortie
   *
   * @return void
   */
  function editFactureBVR($ts = false, $stream = "I") {
    $this->type_pdf = $ts ? "BVR_TS" : "BVR"; 
    $this->editFacture();
    //enregistrement pour chaque facture l'ensemble des factures
    if (count($this->factures)) {
      return $this->pdf->Output($this->facture->cloture."_".$this->patient->nom.'.pdf', $stream);
    }
    else {
      return $this->pdf->Output('Factures.pdf', $stream);
    }
  }

  /**
   * Edition de la facture BVR et du justificatif
   *
   * @param bool $ts tiers soldant
   *
   * @return void
   */
  function editFactureBVRJustif($ts = false) {
    $this->type_pdf = $ts ? "BVR_TS" : "BVR";
    $this->editFacture();
    $this->type_pdf = $ts ? "justif_TS" : "justif";
    $this->editFacture(false);
    //enregistrement pour chaque facture l'ensemble des factures
    if (count($this->factures)) {
      $this->pdf->Output($this->facture->cloture."_".$this->patient->nom.'.pdf', "I");
    }
    else {
      $this->pdf->Output('Factures.pdf', "I");
    }
  }

  /**
   * Edition du justifiactif
   *
   * @param bool   $ts     tiers soldant
   * @param string $stream format de sortie
   *
   * @return void
   */
  function editJustificatif($ts = false, $stream = "I") {
    $this->type_pdf = $ts ? "justif_TS" : "justif"; 
    $this->editFacture();
    if (count($this->factures)) {
      return $this->pdf->Output($this->facture->cloture."_".$this->patient->nom.'.pdf', $stream);
    }
    else {
      return $this->pdf->Output('Justificatifs.pdf', $stream);
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
      $this->pdf->Output("Relance_".$this->facture->cloture."_".$this->patient->nom.'.pdf', "I");
    }
    else {
      $this->pdf->Output('Relances.pdf', "I");
    }
  }

  /**
   * Création du Pdf
   *
   * @return void
   */
  function createPdf() {
    // Creation du PDF
    $this->pdf = new CMbPdf('P', 'mm');
    $this->pdf->setPrintHeader(false);
    $this->pdf->setPrintFooter(false);
    $this->font = "vera";
    $this->fontb = $this->font."b";
  }
  /**
   * Edition de la facture
   *
   * @param bool $create création ou non du pdf
   *
   * @return void
   */
  function editFacture($create = true) {
    if ($create) {
      $this->createPdf();
    }

    foreach ($this->factures as $the_facture) {
      $this->facture = $the_facture;
      $this->facture->loadRefsItems();
      if ($this->facture->cloture && !count($this->facture->_ref_items)) {
        $this->facture->creationLignesFacture();
      }
      
      $this->patient = $this->facture->loadRefPatient();
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
      $adherent = $this->facture->loadNumAdherent($this->praticien->adherent);
      $this->adherent = $adherent["compte"];

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
          $montant = $this->facture->_montant_avec_remise - $this->facture->_reglements_total_patient;
          $montant = $montant - $this->facture->_reglements_total_tiers;
          $this->editHautFacture(" ", $montant);
          $this->editBVR($montant);
        }
        $this->type_pdf = "justif_TS";
        $this->function_prat->adresse = str_replace("\r\n", ' ', $this->function_prat->adresse);
        $this->patient->adresse = str_replace("\r\n", ' ', $this->patient->adresse);
        $this->editCenterJustificatif(0, $montant);
      }
      elseif ($this->type_pdf == "justif") {
        $this->function_prat->adresse = str_replace("\r\n", ' ', $this->function_prat->adresse);
        $this->patient->adresse = str_replace("\r\n", ' ', $this->patient->adresse);
        
        foreach ($this->facture->_montant_factures_caisse as $cle_facture => $montant_facture) {
          $this->editCenterJustificatif($cle_facture, $montant_facture);
        }
      }
      elseif ($this->type_pdf == "relance") {
        $this->editRelanceEntete();
        //$this->editHautFacture(1, $this->relance->_montant, true);
        $this->editBVR($this->relance->_montant);
      }
    }
  }
  
  /**
   * Edition du centre du justificatif
   *
   * @param int $cle_facture     clé de la facture
   * @param int $montant_facture montant de la facture
   *
   * @return void
   */
  function editCenterJustificatif($cle_facture, $montant_facture) {
    $this->loadAllElements();
    $this->pdf->AddPage();
    $pm = $pm_notcoeff = $pt = $pt_notcoeff = $medicaments = 0;
    
    $this->ajoutEntete1();
    $this->pdf->setFont($this->font, '', 8);
    $tailles_colonnes = array(
      "Date" => 7,      "Tarif"=> 4,
      "Code" => 10,      "Code réf" => 6,
      "Sé Cô" => 5,     "Quantité" => 9,
      "Pt PM/Prix" => 8,"fPM" => 5,
      "VPtPM" => 6,     "Pt PT" => 5,
      "fPT" => 4,       "VPtPT" => 4,
      "E" => 2,         "R" => 2,
      "P" => 2,         "M" => 2,
      "Montant" => 10 );
    
    $x = 0;
    $this->pdf->setX(15);
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
        $use_qte_null = CAppUI::conf("dPfacturation Other use_view_quantitynull");
        $qte_null = ($acte->quantite != 0 && $use_qte_null) || !$use_qte_null;

        $tab_tarmed = $cle_facture == 0 && $keytab == "tarmed";
        $tab_caisse = ($cle_facture == 1 && !$acte->use_tarmed_bill) || ($cle_facture == 0 && $acte->type == "CActeCaisse" && $acte->use_tarmed_bill);

        if (($tab_tarmed || ($keytab == "caisse" && $tab_caisse)) && $qte_null) {
          $ligne++;
          $this->pdf->setXY(37, $debut_lignes + $ligne*3);
          //Traitement pour le bas de la page et début de la suivante
          if ($this->pdf->getY() >= 265) {
            $this->pdf->setFont($this->fontb, '', 8);
            $this->editCell($this->pdf->getX(), $debut_lignes + $ligne*3, 130, "Total Intermédiaire", "R");
            $this->pdf->Cell(28, "", round($montant_intermediaire, 2) , null, null, "R");
            $this->pdf->setFont($this->font, '', 8);
            $this->pdf->AddPage();
            $nb_pages++;
            $this->ajoutEntete2($nb_pages);
            $this->editCell(10, $this->pdf->getY()+4, $this->colonnes[0]+$this->colonnes[1], "Patient");
            $this->pdf->Cell($this->colonnes[2], "", $this->patient->nom." ".$this->patient->prenom." ".$this->patient->naissance);
            $this->pdf->Line(10, 42, 190, 42);
            $this->pdf->Line(10, 38, 10, 42);
            $this->pdf->Line(190, 38, 190, 42);
            $ligne = 0;
            $debut_lignes = 50;
            $this->pdf->setXY(10, 0);
          }
          $this->pdf->setFont($this->fontb, '', 7);
          $this->pdf->setXY(37, $debut_lignes + $ligne*3);
         
          $code = "001";
          if ($keytab == "caisse") {
            $code = $acte->code_caisse;
          }
          
          $this->pdf->Write("<b>", substr($acte->libelle, 0, 90));
          $ligne++;
          //Si le libelle est trop long
          if (strlen($acte->libelle)>90) {
            $this->pdf->setXY(37, $debut_lignes + $ligne*3);
            $this->pdf->Write("<b>", substr($acte->libelle, 90));
            $ligne++;
          }
          $x = 0;
          $this->pdf->setX(15);
          $this->pdf->setFont($this->font, '', 8);
          foreach ($tailles_colonnes as $key => $largeur) {
            $valeur = "";
            $cote = "C";
            switch ($key) {
              case "Date" :
                $valeur = $acte->date;
                $valeur= CMbDT::format($valeur, "%d.%m.%Y");
                break;
              case "Tarif":
                $valeur = $code;
                break;
              case "Code réf":
                $valeur = $acte->code_ref;
                break;
              case "Sé Cô":
                $valeur = $acte->seance;
                break;
              case "Quantité":
                $valeur = $acte->quantite;
                break;
              case "Pt PM/Prix":
                $valeur = $acte->pm;
                $cote = "R";
                break;
              case "fPM":
                $valeur = $acte->coeff_pm;
                break;
              case "VPtPM":
              case "VPtPT":
                $valeur = $acte->coeff;
                break;
              case "Pt PT":
                $valeur = $acte->pt;
                $cote = "R";
                break;
              case "fPT":
                $valeur = $acte->coeff_pt;
                break;
              case "Montant":
                $this->pdf->setX($this->pdf->getX()+3);
                $valeur = sprintf("%.2f", $acte->montant_base * $acte->coeff * $acte->quantite);
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
          $this_pt = ($acte->pt * $acte->coeff_pt);
          $this_pm = ($acte->pm * $acte->coeff_pm);
          if (round($acte->montant_base, 2) != round(($this_pt + $this_pm), 2)) {
            $this_pt = 0;
            $this_pm = $acte->montant_base * $acte->coeff;
          }

          $this_pt *= $acte->quantite;
          $this_pm *= $acte->quantite;
          if ($acte->type == "CActeTarmed") {
            $pt += ($this_pt * $acte->coeff);
            $pm += ($this_pm * $acte->coeff);
            $pt_notcoeff += $this_pt;
            $pm_notcoeff += $this_pm;
          }
          else {
            if ($acte->code_caisse) {
              $caisse = new CActeCaisse();
              $caisse->code = $acte->code;
              $caisse->loadRefPrestationCaisse();
              if ($caisse->_ref_caisse_maladie->nom == "Medicament") {
                $medicaments += $acte->montant_base;
              }
            }
          }
          $montant_intermediaire += ($this_pt* $acte->coeff);
          $montant_intermediaire += ($this_pm* $acte->coeff);
        }
      }
    }

    $pt = sprintf("%.2f", $pt);
    $pm = sprintf("%.2f", $pm);
    $pm_notcoeff = sprintf("%.2f", $pm_notcoeff);
    $pt_notcoeff = sprintf("%.2f", $pt_notcoeff);

    $this->pdf->setFont($this->fontb, '', 8);
    $ligne = 265;
    $l = 35;
    $this->editCell(20, $ligne+3, $l, "Tarmed PM", "R");
    $this->pdf->Cell($l, "", "$pm ($pm_notcoeff)", null, null, "R");
    
    $this->editCell(20, $ligne+6, $l, "Tarmed PT", "R");
    $this->pdf->Cell($l, "", "$pt ($pt_notcoeff)", null, null, "R");

    $montant_facture = (abs($montant_intermediaire-$montant_facture) <= 0.09) ? $montant_intermediaire : $montant_facture;
    $autre_temp = $cle_facture == 0 ? $montant_facture - $pm - $pt - $medicaments : $montant_facture;
    $autre_temp = sprintf("%.2f", $autre_temp);
    $autre = ($autre_temp <= 0.05) ? 0.00 : $autre_temp;

    $this->editCell(80, $ligne+3, $l, "Médicaments", "R");
    $this->pdf->Cell(20, "",  sprintf("%.2f", $medicaments), null, null, "R");

    $this->editCell(80, $ligne+6, $l, "Autres", "R");
    $this->pdf->Cell(20, "",  sprintf("%.2f", $autre), null, null, "R");

    $this->editCell(20, $ligne+9, $l, "Montant total/CHF", "R");
    $this->pdf->Cell(20, "", sprintf("%.2f", $montant_intermediaire), null, null, "R");
    
    $acompte = sprintf("%.2f", $this->facture->_reglements_total_patient);
    $this->editCell(80, $ligne+9, $l, "Acompte", "R");
    $this->pdf->Cell(20, "", "".$acompte, null, null, "R");
    
    $total_temp = $montant_intermediaire - $this->facture->_reglements_total_patient;
    $total = $total_temp<0 ? 0.00 : $total_temp;
    
    $this->editCell(130, $ligne+9, $l, "Montant dû", "R");
    $this->pdf->Cell(20, "", sprintf("%.2f", $total), null, null, "R");
  }
  
  /**
   * Calcul des totaux
   * 
   * @return void
   */
  function loadTotaux(){
    $pm = 0;
    $pt = 0;
    $medicaments = 0;
    $this->autre_tarmed = 0;
    foreach ($this->facture->_ref_actes_tarmed as $acte) {
      $tmp_pt = $acte->pt * $acte->coeff_pt * $acte->quantite;
      $tmp_pm = $acte->pm * $acte->coeff_pm * $acte->quantite;
      $pt += $tmp_pt;
      $pm += $tmp_pm;
    }
    
    foreach ($this->facture->_ref_actes_caisse as $acte) {
      $add = true;
      if ($acte->code_caisse) {
        $caisse = new CActeCaisse();
        $caisse->code = $acte->code;
        $caisse->loadRefPrestationCaisse();
        if ($caisse->_ref_caisse_maladie->nom == "Medicament") {
          $medicaments += $acte->montant_base;
          $add = false;
        }
      }
      if ($acte->use_tarmed_bill && $add) {
        $this->autre_tarmed += $acte->montant_base;
      }
    }
    $pt = sprintf("%.2f", $pt * $this->facture->_coeff);
    $pm = sprintf("%.2f", $pm * $this->facture->_coeff);
    
    $this->pre_tab["Medical:"]  = $pm;
    $this->pre_tab["Tarmed:"]   = $pt;
    $this->pre_tab["Médicaments:"] = sprintf("%.2f", $medicaments);
    $autres =  $pm + $pt + $medicaments;
    $total = 0;
    foreach ($this->facture->_montant_factures_caisse as $montant_facture) {
      $total += $montant_facture;
    }
    $this->pre_tab["Autres:"]   = sprintf("%.2f", $total - $autres);
  }

  /**
   * Edition du haut de la relance
   *
   * @return void
   */
  function editRelanceEntete() {
    $this->loadAllElements();
    $this->pdf->AddPage();
    $colonne1 = 10;
    $colonne2 = 120;

    $tab[$colonne1] = array(
      "40" => "Auteur facture",
      $this->auteur["nom"],
      $this->auteur["adresse1"],
      $this->auteur["adresse2"],
      $this->auteur["cp"]." ".$this->auteur["ville"]
    );

    $patient_adrr = $this->traitements($this->patient->adresse);
    //Destinataire de la facture
    $tab[$colonne2] = array(
      "40" => "Patient",
      "n° AVS: ".$this->patient->avs,
      $this->patient->_view,
      $patient_adrr["group1"],
      $patient_adrr["group2"],
      $this->patient->cp." ".$this->patient->ville
    );

    // Ecriture de C, D, E, F
    $this->pdf->setFont($this->font, '', 8);
    $x = $y = 0;
    foreach ($tab as $k => $v) {
      foreach ($v as $key => $value) {
        if ($value) {
          if ($key == "40") {
            $y = $key;
            $x=0;
          }
          $this->editCell($k, $y+$x, 30, $value);
          $x = ($key == "40") ? $x+5 : $x+3;
        }
      }
    }
    $this->editCell(20, $this->pdf->getY()+20, 35, CAppUI::tr("CRelance.statut.".$this->relance->statut), "C", 1, 4);
    $this->pdf->setX(110);
    $this->pdf->Write(4, CAppUI::conf("dPfacturation CEditPdf home_ville", CGroups::loadCurrent()).", le ".CMbDT::format(CMbDT::date(), "%d %B %Y"));

    $frais = 0;
    $messages = array(0 => "", 1 => "");
    $assurance_patient = $this->destinataire[0];
    $type = isset($assurance_patient->type_pec) ? "assur" : "patient";
    switch ($this->relance->statut) {
      case "first":
        $frais = CAppUI::conf("dPfacturation CRelance add_first_relance");
        $messages = explode('/*****/', CAppUI::conf("dPfacturation CRelance message_relance1_$type"));
        break;
      case "second":
        $frais = CAppUI::conf("dPfacturation CRelance add_second_relance");
        $messages = explode('/*****/', CAppUI::conf("dPfacturation CRelance message_relance2_$type"));
        break;
      case "third":
        $frais = CAppUI::conf("dPfacturation CRelance add_third_relance");
        $messages = explode('/*****/', CAppUI::conf("dPfacturation CRelance message_relance3_$type"));
        break;
    }

    if (count($messages) == 1) {
      $messages[1] = "";
    }
    $this->pdf->setXY(10, $this->pdf->getY()+18);
    $this->pdf->Write(3, "Madame, Monsieur,");
    $this->pdf->setXY(10, $this->pdf->getY()+8);
    $this->pdf->Write(4, $messages[0]);

    $y = 122;
    $col1= 40;
    $col2= 80;
    $col3= 30;
    $this->pdf->setFont($this->fontb, '', 8);
    $this->editCell(20, $y, $col1, "Facture", "C", 1, 4);
    $this->editCell(20, $y+4, $col1, "N°: ".$this->facture->_id, null, "LBR", 15);

    $this->editCell($this->pdf->getX(), $y, 80, "Désignation", "C", 1, 4);
    $this->editCell(60, $y+4, $col2, "Du ".CMbDT::format($this->facture->cloture, "%d %B %Y"), null, "R", 5);
    $this->pdf->setFont($this->font, '', 8);
    $this->editCell(60, $y+9, $col2, "Frais", null, "R", 4);
    $this->pdf->setFont($this->fontb, '', 8);
    $this->editCell(60, $y+13, $col2, "Solde à payer", null, "BR", 6);

    $this->editCell($this->pdf->getX(), $y, 30, "Montant (CHF)", "C", 1, 4);
    $this->editCell(140, $y+4, $col3, sprintf('%0.2f', $this->relance->_montant - $frais), "R", "R", 5);
    $this->pdf->setFont($this->font, '', 8);
    $this->editCell(140, $y+9, $col3, sprintf('%0.2f', $frais), "R", "R", 4);
    $this->pdf->setFont($this->fontb, '', 8);
    $this->editCell(140, $y+13, $col3, sprintf('%0.2f', $this->relance->_montant), "R", "BR", 6);

    $this->pdf->setFont($this->font, '', 8);
    $this->pdf->setXY(10, $this->pdf->getY()+14);
    $this->pdf->Write(4, $messages[1]);
    $this->pdf->setXY(120, $this->pdf->getY()+14);
    $this->pdf->Write(4, CAppUI::conf("dPfacturation CEditPdf home_nom", CGroups::loadCurrent()));
    $this->pdf->setXY(120, $this->pdf->getY()+4);
    $this->pdf->Write(3, "Service comptabilité");
  }

  /**
   * Edition du haut de la facture
   *
   * @param int  $cle_facture     clé de la facture
   * @param int  $montant_facture montant de la facture
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

    $patient_adrr = $this->traitements($this->patient->adresse);
    //Destinataire de la facture
    $patient = array(
      "50" => "Destinataire",
      $this->destinataire["nom"],
      $this->destinataire["adresse1"],
      $this->destinataire["adresse2"],
      $this->destinataire["cp"],
      "80" => "Patient",
      "n° AVS: ".$this->patient->avs,
      $this->patient->_view,
      $patient_adrr["group1"],
      $patient_adrr["group2"],
      $this->patient->cp." ".$this->patient->ville
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
    $this->pdf->Cell(25, "", CMbDT::format($this->facture->cloture, "%d %B %Y"), null, null, "L");
    if ($relance) {
      $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "Date relance:", "R");
      $this->pdf->Cell(25, "", CMbDT::format($this->relance->date, "%d %B %Y"), null, null, "L");
    }
    $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "N° facture:", "R");
    $num_fact = $this->facture->_id;
    if (CAppUI::conf("dPfacturation Other use_field_definitive") && !$this->facture->definitive) {
      $num_fact = "PROVISOIRE";
    }
    $this->pdf->Cell(25, "", $num_fact, null, null, "L");

    $use_date_consult = CAppUI::conf("dPfacturation CEditPdf use_date_consult_traitement", CGroups::loadCurrent());
    if ($this->facture->_class == "CFactureCabinet" && count($this->facture->_ref_consults) == 1 && $use_date_consult) {
      $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "Consultation du:", "R");
      $this->pdf->Cell(25, "", CMbDT::format($this->facture->_ref_first_consult->_date, "%d %B %Y"), null, null, "L");
    }
    else {
      $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "Traitement du:", "R");
      $this->pdf->Cell(25, "", CMbDT::format($this->facture->_ref_first_consult->_date, "%d %B %Y"), null, null, "L");
      $this->editCell($colonne1, $this->pdf->GetY()+3, 22, "au:", "R");
      $this->pdf->Cell(25, "", CMbDT::format($this->facture->cloture, "%d %B %Y"), null, null, "L");
    }
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
        $tarif[$cles] = $valeur;
        $montant_total += $valeur;
    }

    if ($relance) {
      $tarif["Relance:"]      = sprintf('%0.2f', $this->relance->_montant);
    }
    $tarif["Remise:"]         = sprintf('%0.2f', -$this->facture->remise);
    $tarif["Montant total:"]  = sprintf('%0.2f', round($montant_total, 1));
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
   * @param int $montant_facture montant du BVR
   *
   * @return void
   */
  function editBVR($montant_facture) {
    $group = CGroups::loadCurrent();
    //le 01 sera fixe car il correspond à un "Codes des genres de justificatifs (BC)" ici :01 = BVR en CHF
    $genre = "01";
    $montant = sprintf("%010s", $montant_facture*100);
    $cle = $this->facture->getNoControle($genre.$montant);
    $num_adherent = $this->facture->loadNumAdherent($this->praticien->adherent);
    $this->adherent2  = $num_adherent["bvr"];
    if (!$this->adherent) {
      $this->adherent = $num_adherent["compte"];
    }
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
      $h_add = !$this->fourn_presta["fct"] ? 3 : 4;
      //Adresse du patient
      $this->pdf->SetTextColor(0);
      $this->pdf->setFont($this->font, '', 8);
      $nom_auteur_conf = CAppUI::conf("dPfacturation CEditPdf home_nom", $group);
      $this->auteur["nom"] = $nom_auteur_conf ? $nom_auteur_conf : $this->auteur["nom"];
      $banque = $this->praticien->loadRefBanque();
      $etab_adresse1 = $banque->_id ? $banque->nom : CAppUI::conf("dPfacturation CEditPdf etab_adresse1", $group);
      $etab_adresse2 = $banque->_id ? $banque->cp." ".$banque->ville : CAppUI::conf("dPfacturation CEditPdf etab_adresse2", $group);

      if ($etab_adresse1) {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $etab_adresse1);
        $h_add++;
        if ($etab_adresse2) {
          $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $etab_adresse2);
          $h_add++;
        }
        $h_add++;
      }

      if (!$this->fourn_presta["fct"]) {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $this->auteur["nom"]);
      }
      else {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $this->auteur["nom"]);
        $h_add++;
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $this->fourn_presta["fct"]);
      }

      $h_add++;
      $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $this->auteur["adresse1"]);
      $h_add++;
      if ($this->auteur["adresse2"]) {
        $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $this->auteur["adresse2"]);
        $h_add++;
      }
      $this->pdf->Text($l_colonne + $decalage, $h_ligne*$h_add+$haut_doc , $this->auteur["cp"]." ".$this->auteur["ville"]);
      
      //Numéro adhérent, CHF, Montant1 et Montant2
      $this->pdf->Text($l_colonne*11 + $decalage, $h_ligne*10.75+$haut_doc , $this->adherent);
      
      $this->pdf->setFont($this->font, '', 10);
      $placement_colonne = $l_colonne*(17-strlen($montant_facture*100)) + $decalage;
      $this->pdf->Text($placement_colonne, $h_ligne*13+$haut_doc, sprintf("%d", $montant_facture));
      
      $cents = floor(sprintf("%.2f", $montant_facture - sprintf("%d", $montant_facture))*100);
      if ($cents<10) {
        $cents = "0".$cents;
      }
      $this->pdf->Text($l_colonne*19 + $decalage, $h_ligne*13+$haut_doc , $cents);
    }
    $decalage = $left_offset; // 7.36 // 8;
    
    //Ecriture de la reference
    $num_reference = preg_replace('/(\d{2})(\d{5})(\d{5})(\d{5})(\d{5})/', '\1 \2 \3 \4 \5 \6', $this->facture->num_reference);
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
    if ($this->facture->type_facture == "accident" && $this->facture->assurance_accident && $this->facture->_ref_assurance_accident->relation == "employeur") {
      $employeur = $this->facture->_ref_assurance_accident;
      $_ref_assurance = $employeur->num_assure;
      $nom_entreprise = $employeur->nom;
    }
    
    $loi = $this->facture->type_facture == "accident" ? "LAA" : "LAMal";
    $conf_cab = CAppUI::conf("tarmed coefficient pt_maladie", CGroups::loadCurrent());
    if ($this->facture->type_facture == "accident" && $this->facture->_coeff == $conf_cab) {
      $loi = "LAMal";
    }
    if ($this->facture->statut_pro == "invalide") {
      $loi = "LAI";
    }
    if ($this->facture->statut_pro == "militaire") {
      $loi = "AMF";
    }
    
    $assurance_patient = $this->destinataire[0];
    $assur_nom = "";
    if ($this->facture->_class != "CFactureCabinet" && $this->facture->dialyse && $this->facture->_ref_assurance_accident) {
      $assur_nom = $this->facture->_ref_assurance_accident->nom." ".$this->facture->_ref_assurance_accident->prenom;
    }
    if (isset($assurance_patient->type_pec) && $assurance_patient->type_pec == "TS" && $this->type_rbt == "TG avec cession") {
      if (count($this->facture->_ref_reglements) && $this->type_pdf == "justif_TS") {
        $assur_nom = $this->patient->nom." ".$this->patient->prenom;
      }
      else {
        $assur_nom = "$assurance_patient->nom $assurance_patient->prenom";
      }
      $assurance_patient = $this->patient;
    }
    
    $assur = array();
    $assur["civilite"]  = isset($assurance_patient->civilite) ? ucfirst($this->patient->civilite) : "";
    $assur["nom"]     = "$assurance_patient->nom $assurance_patient->prenom";
    $assur["adresse"] = "$assurance_patient->adresse";
    $assur["cp"]      = "$assurance_patient->cp $assurance_patient->ville";
    
    $motif = $this->facture->type_facture;

    if ($this->facture->type_facture == "accident" && $this->facture->_coeff == $conf_cab) {
      $motif = "Accident (Caisse-Maladie)";
    }
    $naissance =  CMbDT::format($this->patient->naissance, "%d.%m.%Y");
    $colonnes = array(20, 28, 25, 25, 35, 50);
    if ($this->facture->_class == "CFactureCabinet") {
      $traitement = CMbDT::format($this->facture->_ref_first_consult->_date, "%d.%m.%Y")." - ";
      $traitement .= CMbDT::format($this->facture->_ref_last_consult->_date, "%d.%m.%Y");
    }
    else {
      $traitement = CMbDT::format($this->facture->_ref_first_sejour->entree, "%d.%m.%Y")." - ";
      $traitement .= CMbDT::format($this->facture->_ref_first_sejour->sortie, "%d.%m.%Y");
    }

    $name_rappel = $date_rappel = null;
    if (CAppUI::conf("dPfacturation CRelance use_relances")) {
      //$name_rappel = "Date rappel";
      //$date_rappel = CMbDT::date("+".CAppUI::conf("dPfacturation CRelance nb_days_first_relance")." DAY" , $this->facture->cloture);
      //$date_rappel = CMbDT::format($date_rappel, "%d.%m.%Y");
    }
    $date_cas = ($this->facture->date_cas && $this->facture->type_facture == "accident") ? CMbDT::format($this->facture->date_cas, "%d.%m.%Y") : "";
    $ref_accident = (($this->facture->ref_accident || $this->facture->statut_pro == "invalide")) ? $this->facture->ref_accident : "";

    $ean2 = $this->group->ean;
    if ($this->facture->_class == "CFactureEtablissement" && $this->facture->_ref_last_sejour->_ref_last_operation) {
      $ean2 = $this->facture->_ref_last_sejour->_ref_last_operation->_ref_anesth->ean;
    }
    $num_fact = $this->facture->_id;
    if (CAppUI::conf("dPfacturation Other use_field_definitive") && !$this->facture->definitive) {
      $num_fact = "PROVISOIRE";
    }
    $see_diag_justificatif = CAppUI::conf("dPfacturation CEditPdf see_diag_justificatif", CGroups::loadCurrent());
    $msg_info = $see_diag_justificatif ? "U / Toute demande d'information est à adresser au chirurgien ".$this->praticien->_view : "";
    $lignes = array(
      array("Patient"   , "Nom"             , $this->patient->nom     ,null, "Assurance", $assur_nom),
      array(""          , "Prénom"          , $this->patient->prenom),
      array(""          , "Rue"             , $this->patient->adresse),
      array(""          , "NPA"             , $this->patient->cp      , null, $assur["civilite"]),
      array(""          , "Localité"        , $this->patient->ville   , null, $assur["nom"]),
      array(""          , "Date de naissance",$naissance        , null, $assur["adresse"]),
      array(""          , "Sexe"            , strtoupper($this->patient->sexe) , null, $assur["cp"]),
      array(""          , "Date cas"        , $date_cas),
      array(""          , "N° cas"          , $ref_accident),
      array(""          , "N° AVS"          , $this->patient->avs),
      array(""          , "N° assuré"       , $_ref_assurance),
      array(""          , "Nom entreprise"  , $nom_entreprise),
      array(""          , "Canton"          , CAppUI::conf("dPfacturation CEditPdf canton", CGroups::loadCurrent())),
      array(""          , "Copie"           , "Non"),
      array(""          , "Type de remb."   , $this->type_rbt),
      array(""          , "Loi"             , $loi),
      array(""          , "N° contrat"      , ""),
      array(""          , "Motif traitement", $motif  , null, "N° facture", $num_fact),
      array(""          , "Traitement"      , $traitement, null, "Date facture", CMbDT::format($this->facture->cloture, "%d.%m.%Y")),
      array(""          , "Rôle/ Localité"  , "-", null, $name_rappel, $date_rappel),
      array("Mandataire", "N° EAN/N° RCC"   , $this->praticien->ean." - ".$this->praticien->rcc, null, $this->praticien->_view),
      array("Diagnostic", $msg_info),
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
    $auteur = substr($this->auteur["adresse1"], 0, 29)." ". $this->auteur["cp"]." ".$this->auteur["ville"];
    $presta = $this->fourn_presta;
    $presta_adresse = substr($presta["adresse1"], 0, 29)." ". $presta["0"]->cp." ".$presta["0"]->ville;
    $lignes = array(
      array("Document"    , "Identification", $this->facture->_id." ".CMbDT::format(null, "%d.%m.%Y %H:%M:%S"), "", "Page $nb"),
      array("Auteur"      , "N° EAN(B)"     , $this->auteur["EAN"], $this->auteur["nom"], " Tél: ".$this->auteur["tel"]),
      array("Facture"     , "N° RCC(B)"     , $this->auteur["RCC"], $auteur, "Fax: ".$this->auteur["fax"]),
      array("Four.de"     , "N° EAN(P)"     , $presta["EAN"], $presta["nom_dr"], " Tél: ".$presta["0"]->tel),
      array("prestations" , "N° RCC(B)"     , $presta["RCC"], $presta_adresse, "Fax: ".$presta["0"]->fax)
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

    /*
    if (strlen($this->function_prat->cp)>4) {
      $this->function_prat->cp =  substr($this->function_prat->cp, 1);
    }
    if (strlen($this->patient->cp)>4) {
      $this->patient->cp =  substr($this->patient->cp, 1);
    }
    */

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
      if ($this->facture->_coeff == CAppUI::conf("tarmed coefficient pt_maladie", CGroups::loadCurrent())) {
        $this->type_rbt = $this->facture->_ref_assurance_accident->type_pec;
      }
      else {
        $this->type_rbt = "TP";
      }

      if ($this->type_rbt == "TG") {
        $assurance_patient = $this->patient;
      }
      else {
        $assurance_patient = $this->facture->_ref_assurance_accident;
      }
    }
    else {
      $assurance_patient = $this->patient;
      $view = "_view";
    }
    if (count($this->facture->_ref_reglements) && $this->type_pdf == "BVR_TS") {
      $assurance_patient = $this->patient;
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

    $group = CGroups::loadCurrent()->_guid;
    if (!CAppUI::conf("dPfacturation CEditPdf use_bill_etab", $group)) {
      $this->fourn_presta = array(
        "0"        =>  $this->function_prat,
        "nom_dr"   =>  "Dr. ".$this->praticien->_view,
        "fct"      =>  $this->function_prat->_view,
        "adresse1" =>  $this->adresse_prat["group1"],
        "adresse2" =>  $this->adresse_prat["group2"],
        "EAN"      =>  $this->praticien->ean,
        "RCC"      =>  $this->praticien->rcc
      );
      if (!CAppUI::conf("dPfacturation CEditPdf use_bill_fct", $group)) {
        $this->fourn_presta["fct"] = "";
      }
    }
    else {
      $this->fourn_presta = $this->auteur;
      $this->fourn_presta["nom_dr"] = $this->group->raison_sociale;
      $this->fourn_presta["fct"] = "";
      $this->auteur["nom"]      = CAppUI::conf("dPfacturation CEditPdf home_nom", $group);
      $this->auteur["adresse1"] = CAppUI::conf("dPfacturation CEditPdf home_adresse", $group);
      $this->auteur["adresse2"] = "";
      $this->auteur["cp"]       = CAppUI::conf("dPfacturation CEditPdf home_cp", $group);
      $this->auteur["ville"]    = CAppUI::conf("dPfacturation CEditPdf home_ville", $group);
      $this->auteur["EAN"]      = CAppUI::conf("dPfacturation CEditPdf home_EAN", $group);
      $this->auteur["RCC"]      = CAppUI::conf("dPfacturation CEditPdf home_RCC", $group);
      $this->auteur["tel"]      = CAppUI::conf("dPfacturation CEditPdf home_tel", $group);
      $this->auteur["fax"]      = CAppUI::conf("dPfacturation CEditPdf home_fax", $group);
    }
  }

  /**
   * Impression des factures
   *
   * @param bool $ts tiers soldant
   *
   * @return void
   */
  function printBill($ts = false){
    if (count($this->factures)) {
      $user = CMediusers::get();
      $printer_bvr = new CPrinter();
      $printer_bvr->function_id = $user->function_id;
      $printer_bvr->label = "bvr";
      $printer_bvr->loadMatchingObject();

      $printer_justif = new CPrinter();
      $printer_justif->function_id = $user->function_id;
      $printer_justif->label = "justif";
      $printer_justif->loadMatchingObject();

      if (!$printer_bvr->_id || !$printer_justif->_id) {
        CAppUI::setMsg("Les imprimantes ne sont pas paramétrées", UI_MSG_ERROR);
        echo CAppUI::getMsg();
        return false;
      }
      $file = new CFile();

      foreach ($this->factures as $facture) {
        $facture_pdf = new CEditPdf();
        $facture_pdf->factures = array($facture);
        $pdf = "";
        $pdf = $facture_pdf->editFactureBVR($ts, "S");
        $file_path = tempnam("tmp", "facture");
        $file->_file_path = $file_path;
        file_put_contents($file_path, $pdf);
        $printer_bvr->loadRefSource()->sendDocument($file);
        unlink($file_path);

        $pdf = "";
        $pdf = $facture_pdf->editJustificatif($ts, "S");
        $file_path = tempnam("tmp", "facture");
        $file->_file_path = $file_path;
        file_put_contents($file_path, $pdf);
        $printer_justif->loadRefSource()->sendDocument($file);
        unlink($file_path);
      }
    }
  }
}