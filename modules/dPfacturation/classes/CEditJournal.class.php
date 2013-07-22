<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Classe permettant de créer les journaux au format pdf
 */
class CEditJournal {
  //Elements du PDF
  public $type_pdf;
  /** @var CMbPdf*/
  public $pdf;
  public $font;
  public $fontb;
  public $size;
  public $page;
  public $date_min;
  public $date_max;

  /** @var CReglement[] $reglements */
  public $reglements;
  /** @var CRelance[] $relances*/
  public $relances;
  /** @var CFactureEtablissement[] $factures*/
  public $factures;

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
   * Fontion qui permet l'écriture des tableaux de données
   *
   * @param array $colonnes les noms et largeurs de colonnes
   * @param int   $_x       position du curseur à placer en x
   * @param int   $y        position du curseur à placer en y
   *
   * @return void
   */
  function editTableau($colonnes, $_x, $y) {
    $x = 0;
    $this->pdf->setXY($_x, $y);
    foreach ($colonnes as $key => $value) {
      $this->editCell($this->pdf->getX()+$x, $y, $value, $key);
      $x = $value;
    }
  }

  /**
   * Edition des journaux selon le type
   *
   * @return void
   */
  function editJournal() {
    // Creation du PDF
    $this->pdf = new CMbPdf('l', 'mm');
    $this->pdf->setPrintHeader(false);
    $this->pdf->setPrintFooter(false);
    $this->font = "vera";
    $this->fontb = $this->font."b";
    $this->pdf->setFont($this->font, '', 8);

    $this->page = 0;
    $this->editEntete();

    $this->pdf->Line(5, 5, 5, 205);
    $this->pdf->Line(5, 5, 293, 5);
    $this->pdf->Line(293, 5, 293, 205);
    $this->pdf->Line(5, 205, 293, 205);

    switch ($this->type_pdf) {
      case "paiement" :
        $this->editPaiements();
        break;
      case "debiteur" :
        $this->editDebiteur();
        break;
      case "rappel" :
        $this->editRappel();
        break;
    }
    //Affichage du fichier pdf
    $this->pdf->Output('Factures.pdf', "I");
  }

  /**
   * Edition de l'entete des journaux
   *
   * @return void
   */
  function editEntete() {
    $this->page ++;
    $this->pdf->AddPage();
    $this->pdf->setFont($this->font, '', 10);
    $nom_journal = "JOURNAL DES PAIEMENTS";
    switch ($this->type_pdf) {
      case "paiement" :
        $nom_journal = "Journal des paiements";
        break;
      case "debiteur" :
        $nom_journal = "Journal de facturation";
        break;
      case "rappel" :
        $nom_journal = "Journal rappels/contentieux";
        break;
    }
    $this->editCell(10, 10, 70, CAppUI::conf("dPfacturation CEditPdf home_nom"));
    $this->pdf->Cell(160, "", $nom_journal, null, null, "C");
    $this->pdf->Cell(67, "", "Page: ".$this->page);
    $date = "Date: du ".CMbDT::transform("", $this->date_min, '%d/%m/%Y')." au ".CMbDT::transform("", $this->date_max, '%d/%m/%Y');
    $this->editCell(10, 15, 70, $date);
    $this->pdf->setFont($this->font, '', 8);
    $this->pdf->Line(5, 20, 293, 20);
    $this->pdf->Line(5, 30, 293, 30);
  }

  /**
   * Edition du journal des paiements
   *
   * @return void
   */
  function editPaiements() {
    $colonnes = array(
      "Date"        => 10,  "Nom"       => 25,
      "Garant"      => 25,  "Libellé"   => 35,
      "Facture"     => 10,  "Débit"     => 15,
      "Crédit C/C"  => 15, "Solde fact." => 15);
    $this->editTableau($colonnes, 5, 25);

    $debut_lignes = 30;
    $ligne = 0;
    foreach ($this->reglements as $reglement) {
      $reglement->_ref_facture->loadRefsReglements();
      $this->pdf->setX(5);
      $ligne++;
      $valeurs = array(
        "Date"    => CMbDT::transform("", $reglement->date, '%d/%m/%Y'),
        "Nom"     => $reglement->_ref_facture->_ref_patient->nom." ".$reglement->_ref_facture->_ref_patient->prenom,
        "Garant"  => $this->loadGarant($reglement->_ref_facture),
        "Libellé" => $reglement->mode,
        "Facture" => $reglement->_ref_facture->_id,
        "Débit"   => "",
        "Crédit C/C" => sprintf("%.2f", $reglement->montant),
        "Solde fact." => sprintf("%.2f", $reglement->_ref_facture->_du_restant_patient));

      if ($reglement->debiteur_desc) {
        $valeurs["Libellé"] .= " ($reglement->debiteur_desc)";
      }
      $x = 0;
      foreach ($colonnes as $key => $value) {
        $cote = ($key == "Crédit C/C" || $key == "Solde fact.") ? "R" : "L";
        $this->editCell($this->pdf->getX()+$x, $debut_lignes + $ligne*4, $value, $valeurs[$key], $cote);
        $x = $value;
      }
      if ($debut_lignes + $ligne*4 >= 200) {
        $this->editEntete();
        $this->editTableau($colonnes, 5, 25);
        $ligne = 0;
      }
    }
  }

  /**
   * Edition du journal des débiteurs
   *
   * @return void
   */
  function editDebiteur() {
    $colonnes = array(
      "Facture"     => 10,  "Date Fact."  => 10,
      "T.adm."     => 6,    "Nom"         => 35,
      "Séjour du"   => 10, "Séjour au"    => 10,
      "Total Fact." => 15, "Acomptes"     => 15,
      "Net à payer" => 15, "Echéance"     => 10,
      "Extourne"    => 5);
    $this->editTableau($colonnes, 5, 25);

    $debut_lignes = 30;
    $ligne = 0;
    foreach ($this->factures as $facture) {
      $this->pdf->setX(5);
      $ligne++;
      $valeurs = array(
        "Facture"     => $facture->_id,
        "Date Fact."  => CMbDT::transform("", $facture->cloture, '%d/%m/%Y'),
        "T.adm."      => "AMBU",
        "Nom"         => $facture->_ref_patient->_view,
        "Séjour du"   => CMbDT::transform("", $facture->_ref_last_sejour->entree_prevue, '%d/%m/%Y'),
        "Séjour au"   => CMbDT::transform("", $facture->_ref_last_sejour->sortie_prevue, '%d/%m/%Y'),
        "Total Fact." => sprintf("%.2f", $facture->_montant_avec_remise),
        "Acomptes"    => sprintf("%.2f", $facture->_reglements_total_patient),
        "Net à payer" => sprintf("%.2f", $facture->_du_restant_patient),
        "Echéance"    => CMbDT::transform("", $facture->_echeance, '%d/%m/%Y'),
        "Extourne"    => $facture->annule ? "Oui" : "");

      $x = 0;
      foreach ($colonnes as $key => $value) {
        $cote = ($key == "Net à payer" || $key == "Total Fact." || $key == "Acomptes") ? "R" : "L";
        $this->editCell($this->pdf->getX()+$x, $debut_lignes + $ligne*4, $value, $valeurs[$key], $cote);
        $x = $value;
      }
      if ($debut_lignes + $ligne*4 >= 200) {
        $this->editEntete();
        $this->editTableau($colonnes, 5, 25);
        $ligne = 0;
      }
    }
  }

  /**
   * Edition du journal des rappels
   *
   * @return void
   */
  function editRappel() {
    $colonnes = array(
      "Concerne" => 25, "Destinataire"  => 25,
      "N° fact." => 10, "Débit"         => 15,
      "Crédit"   => 15, "Solde"     => 15,
      "Echéance" => 10, "Pas de rappel jusqu'au" => 15);
    $this->editTableau($colonnes, 5, 25);

    $debut_lignes = 30;
    $ligne = 0;
    foreach ($this->relances as $relance) {
      $this->pdf->setX(5);
      $ligne++;
      $valeurs = array(
        "Concerne"      => $relance->_ref_object->_ref_patient->nom." ".$relance->_ref_object->_ref_patient->prenom,
        "Destinataire"  => $this->loadGarant($relance->_ref_object),
        "N° fact."      => $relance->_ref_object->_id,
        "Débit"         => sprintf("%.2f", $relance->_ref_object->_montant_avec_remise),
        "Crédit"        => sprintf("%.2f", $relance->_ref_object->_reglements_total_patient),
        "Solde"         => sprintf("%.2f", $relance->_ref_object->_du_restant_patient),
        "Echéance"      => CMbDT::transform("", $relance->_ref_object->_echeance, '%d/%m/%Y'),
        "Pas de rappel jusqu'au" => CMbDT::transform("", CMbDT::date("+1 DAY", $relance->date), '%d/%m/%Y'));
      $x = 0;
      foreach ($colonnes as $key => $value) {
        $cote = ($key == "Débit" || $key == "Crédit" || $key == "Solde") ? "R" : "L";
        $this->editCell($this->pdf->getX()+$x, $debut_lignes + $ligne*4, $value, $valeurs[$key], $cote);
        $x = $value;
      }
      if ($debut_lignes + $ligne*4 >= 200) {
        $this->editEntete();
        $this->editTableau($colonnes, 5, 25);
        $ligne = 0;
      }
    }
  }

  /**
   * Chargement du garant de la facture
   *
   * @param CFactureCabinet|CFactureEtablissement $facture la facture
   *
   * @return string
   */
  function loadGarant($facture) {
    $patient = $facture->_ref_patient;
    $facture->loadRefAssurance();
    if (strlen($patient->cp)>4) {
      $patient->cp =  substr($patient->cp, 1);
    }

    $assurance_patient = null;
    $view = "_longview";
    $send_assur = !$facture->send_assur_base && $facture->type_facture == "maladie";
    if ($facture->assurance_maladie && $send_assur && $facture->_ref_assurance_maladie->type_pec != "TG" ) {
      $assurance_patient = $facture->_ref_assurance_maladie;
    }
    elseif ($facture->assurance_accident && !$facture->send_assur_compl && $facture->type_facture == "accident") {
      $assurance_patient = $this->type_rbt == "TG" ? $patient : $facture->_ref_assurance_accident;
    }
    else {
      $assurance_patient = $patient;
      $view = "_view";
    }
    return $assurance_patient->$view;
  }
}