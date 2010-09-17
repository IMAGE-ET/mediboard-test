<?php /* $Id: vw_idx_etiquette.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
CAppUI::requireSystemClass('mbMetaObject');
class CModeleEtiquette extends CMbMetaObject {
	
	// DB Table key
  var $modele_etiquette_id = null;
  
  // DB Fields
  var $nom           = null;
  var $texte         = null;
  var $largeur_page  = null;
  var $hauteur_page = null;
  var $nb_lignes     = null;
  var $nb_colonnes   = null;
  var $marge_horiz   = null;
  var $marge_vert    = null;
  var $hauteur_ligne = null;
  var $font          = null;
  
  static $fields = array("CPatient" =>
      array("[DATE NAISS]", "[DMED]", "[NDOS]", 
            "[NOM]", "[NOM JF]",  "[NUM SECU]",
            "[PRENOM]", "[SEXE]"),
      "CSejour" => array("[DATE ENT]", "[PRAT RESPONSABLE]"));
  
  static $listfonts =
      array("dejavusansmono" => "DejaVu Sans Mono",
            "freemono"       => "Free Mono",
            "veramo"         => "Vera Sans Mono");
      
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modele_etiquette';
    $spec->key   = 'modele_etiquette_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]           = "str notNull";
    $specs["texte"]         = "text notNull";
    $specs["largeur_page"]  = "float notNull default|21";
    $specs["hauteur_page"]  = "float notNull default|29.7";
    $specs["marge_horiz"]   = "float notNull default|0.3";
    $specs["marge_vert"]    = "float notNull default|1.3";
    $specs["nb_lignes"]     = "num notNull default|8";
    $specs["nb_colonnes"]   = "num notNull default|4";
    $specs["hauteur_ligne"] = "float notNull default|8";
    $specs["object_id"]     = "ref class|CMbObject meta|object_class purgeable";
    $specs["object_class"]  = "str notNull class show|0";
    $specs["font"]          = "text show|0";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->_view = $this->nom;
  }
  
  function replaceFields($array_fields) {
  	foreach($array_fields as $_key=>$_field) {
  		str_replace($_key, $_field, $this->text);
  	}
  }
  
  function printEtiquettes() {
  	// Affectation de la police par défault si aucune n'est choisie
		if ($this->font == "")
		  $this->font = "dejavusansmono";
		
		// Calcul des dimensions de l'étiquette
		$largeur_etiq = ($this->largeur_page - 2 * $this->marge_horiz) / $this->nb_colonnes;
		$hauteur_etiq = ($this->hauteur_page - 2 * $this->marge_vert) / $this->nb_lignes;
		
		// Création du PDF
		$pdf = new CMbPdf('P', 'cm', array($this->largeur_page, $this->hauteur_page));
		$pdf->setFont($this->font, '', $this->hauteur_ligne);
		
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		$pdf->SetMargins($this->marge_horiz, $this->marge_vert, $this->marge_horiz);
		$pdf->SetAutoPageBreak(0, $this->marge_vert);
		
		$pdf->AddPage();
		
		// Création de la grille d'étiquettes et écriture du contenu.
		for ($i = 0; $i < $this->nb_lignes * $this->nb_colonnes; $i++) {
		  if (round($pdf->GetX()) >= $this->largeur_page ) {
		    $pdf->SetX(0);
		    $pdf->SetLeftMargin($this->marge_horiz);
		    $pdf->SetY($pdf->GetY() + $hauteur_etiq);
		  }
		  $pdf->Rect($pdf->GetX(),$pdf->GetY(),$largeur_etiq, $hauteur_etiq, 'D');
		  $x = $pdf->GetX();
		  $y = $pdf->GetY();
		  $pdf->SetLeftMargin($x);
		  
		  // On affecte la marge droite de manière à ce que la méthode Write fasse un retour chariot
		  // lorsque le contenu écrit va dépasser la largeur de l'étiquette
		  $pdf->SetRightMargin($this->largeur_page - $x - $largeur_etiq);
		  $pdf->Write($this->hauteur_ligne / 20, $this->texte);
		  $x = $x + $largeur_etiq;
		  $pdf->SetY($y);
		  $pdf->SetX($x);
		}
		$pdf->Output($this->nom.'.pdf',"I");
  }
}
?>