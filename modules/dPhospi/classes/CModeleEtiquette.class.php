<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CModeleEtiquette extends CMbMetaObject {
  
  // DB Table key
  public $modele_etiquette_id;
  
  // DB Fields
  public $nom;
  public $texte;
  public $texte_2;
  public $texte_3;
  public $texte_4;
  public $largeur_page;
  public $hauteur_page;
  public $nb_lignes;
  public $nb_colonnes;
  public $marge_horiz;
  public $marge_vert;
  public $hauteur_ligne;
  public $font;
  public $group_id;
  public $show_border;
  public $text_align;

  // Form fields
  public $_write_bold;
  public $_write_upper;
  public $_width_etiq;
  public $_height_etiq;

  static $fields;
  
  static $listfonts = array(
    "dejavusansmono" => "DejaVu Sans Mono",
    "freemono"       => "Free Mono",
    "veramo"         => "Vera Sans Mono",
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modele_etiquette';
    $spec->key   = 'modele_etiquette_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]           = "str notNull";
    $specs["texte"]         = "text notNull";
    $specs["texte_2"]       = "text";
    $specs["texte_3"]       = "text";
    $specs["texte_4"]       = "text";
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
    $specs["group_id"]      = "ref class|CGroups notNull";
    $specs["show_border"]   = "bool default|0";
    $specs["text_align"]    = "enum list|top|middle|bottom default|top";
    $specs["_write_bold"]   = "bool";
    $specs["_write_upper"]  = "bool";
    $specs["_width_etiq"]   = "float";
    $specs["_height_etiq"]  = "float";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->_view = $this->nom;
    $this->_width_etiq = round(($this->largeur_page - 2 * $this->marge_horiz) / $this->nb_colonnes, 2);
    $this->_height_etiq = round(($this->hauteur_page - 2 * $this->marge_vert) / $this->nb_lignes, 2);
  }
  
  function replaceFields($array_fields) {
    $search = array();
    $replace = array();
    foreach ($array_fields as $_key=>$_field) {
      // Normal
      $search[]  = "[$_key]";
      $replace[] = $_field;
      // Gras
      $search[]  = "*$_key*";
      $replace[] = "<b>$_field</b>";
      // Majuscule
      $search[]  = "+$_key+";
      $replace[] = strtoupper($_field);
      // Gras + majuscule
      $search[]  = "#$_key#";
      $replace[] = "<b>".strtoupper($_field)."</b>";
    }

    $this->texte   = str_replace($search, $replace, $this->texte);
    $this->texte_2 = str_replace($search, $replace, $this->texte_2);
    $this->texte_3 = str_replace($search, $replace, $this->texte_3);
    $this->texte_4 = str_replace($search, $replace, $this->texte_4);
  }
  
  function completeLabelFields(&$fields, $params) {
    $fields = array_merge($fields, array(
      "DATE COURANTE"  => CMbDT::dateToLocale(CMbDT::date()),
      "HEURE COURANTE" => CMbDT::format(null, "%H:%M")
    ));
  }
  
  function printEtiquettes($printer_id = null, $stream = 1) {
    // Affectation de la police par défault si aucune n'est choisie
    if ($this->font == "") {
      $this->font = "dejavusansmono";
    }
    
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
    
    $distinct_texts = 1;
    $textes = array();
    $pays = CAppUI::conf("ref_pays");

    // La fonction nl2br ne fait qu'ajouter la balise <br />, elle ne supprime pas le \n.
    // Il faut donc le faire manuellement.
    $textes[1] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte)));

    if ($this->texte_2) {
      $distinct_texts++;
      $textes[] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte_2)));
    }
    
    if ($this->texte_3) {
      $distinct_texts++;
      $textes[] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte_3)));
    }
    
    if ($this->texte_4) {
      $distinct_texts++;
      $textes[] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte_4)));
    }

    $nb_etiqs = $this->nb_lignes * $this->nb_colonnes;
    $increment = floor($nb_etiqs / $distinct_texts);
    $current_text = 1;

    // Création de la grille d'étiquettes et écriture du contenu.
    for ($i = 0; $i < $nb_etiqs; $i++) {
      if ($i != 0 && $i % $increment == 0 && isset($textes[$current_text+1])) {
        $current_text++;
      }

      if (round($pdf->GetX()) >= round($this->largeur_page - 2 * $this->marge_horiz)) {
        $pdf->SetX(0);
        $pdf->SetLeftMargin($this->marge_horiz);
        $pdf->SetY($pdf->GetY() + $hauteur_etiq);
      }
      
      if ($this->show_border) {
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $largeur_etiq, $hauteur_etiq, 'D');
      }
      
      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->SetLeftMargin($x);
      
      // On affecte la marge droite de manière à ce que la méthode Write fasse un retour chariot
      // lorsque le contenu écrit va dépasser la largeur de l'étiquette
      $pdf->SetRightMargin($this->largeur_page - $x - $largeur_etiq);
      
      $fragments = explode("@", $textes[$current_text]);
      $was_barcode = 0;
      CMbArray::removeValue("", $fragments);
      
      // Evaluation de la hauteur du contenu de la cellule
      // si un alignement spécifique est demandé.
      if ($this->text_align != "top") {
        $pdf_ex = new CMbPdf('p', 'cm', array($largeur_etiq, $hauteur_etiq));
        
        $pdf_ex->setFont($this->font, '', $this->hauteur_ligne);
        $pdf_ex->SetMargins(0, 0, 0);
        $pdf_ex->setPrintHeader(false);
        $pdf_ex->setPrintFooter(false);
        $pdf_ex->SetAutoPageBreak(false);
        
        $pdf_ex->AddPage();
        
        foreach ($fragments as $fragment) {
          if (preg_match("/BARCODE_(.*)/", $fragment, $matches) == 1) {
            switch ($pays) {
              case "2":
                $save_x = $pdf_ex->getX();
                $pdf_ex->setY($pdf_ex->getY() + 0.4);
                $pdf_ex->setX($save_x);
                $barcode = $matches[1];
                $pdf_ex->setFont("C39HrP24DhTt", '', 30);
                $pdf_ex->WriteHTML($barcode, false);
                $pdf_ex->setFont($this->font, '', $this->hauteur_ligne);
                break;
              default:
                $barcode_x = $pdf_ex->getX() + 0.15;
                $barcode_y = $pdf_ex->getY();
                $barcode = $matches[1];
                $barcode_width = strlen($barcode) * 0.4 + 0.4;
                $pdf_ex->writeBarcode($barcode_x, $barcode_y, $barcode_width, 0.8, "C128B", 1, null, null, $barcode, 25);
                $pdf_ex->setX($barcode_x + $barcode_width);
            }
            $was_barcode = 1;
          }
          else {
            if ($was_barcode) {
              $sub_fragments = explode("<br />", $fragment, 2);
              $pdf_ex->WriteHTML($sub_fragments[0], false);
              if (isset($sub_fragments[1])) {
                $actual_y = $pdf_ex->getY();
                $pdf_ex->setY($actual_y+0.8);
                $pdf_ex->WriteHTML($sub_fragments[1], false);
              }
            }
            else {
              $pdf_ex->WriteHTML($fragment, false);
            }
            $was_barcode = 0;
          }
          
        }

        $pdf_y = $pdf->getY();
        $pdf_ex_y = $pdf_ex->getY();
        
        switch ($this->text_align) {
          case "middle":
            $pdf->setY($pdf_y - 0.2 + ($hauteur_etiq - $pdf_ex_y) / 2);
            break;
          case "bottom":
            $pdf->setY($pdf_y - 0.4 + $hauteur_etiq - $pdf_ex_y);
        }
      }

      foreach ($fragments as $fragment) {
        if (preg_match("/BARCODE_(.*)/", $fragment, $matches) == 1) {
          switch ($pays) {
            case "2":
              // La position x est à remettre car perdue lors de la méthode setY
              $save_x = $pdf->getX();
              $pdf->setY($pdf->getY() + 0.4);
              $pdf->setX($save_x);
              $barcode = $matches[1];
              $pdf->setFont("C39HrP24DhTt", '', 30);
              $pdf->WriteHTML("*$barcode*", false);
              $pdf->setFont($this->font, '', $this->hauteur_ligne);
              break;
            default:
              $barcode_x = $pdf->getX() + 0.15;
              $barcode_y = $pdf->getY();
              $barcode = $matches[1];
              $barcode_width = strlen($barcode) * 0.4 + 0.4;
              $pdf->writeBarcode($barcode_x, $barcode_y, $barcode_width, 0.8, "C128B", 1, null, null, $barcode);
              $pdf->setX($barcode_x + $barcode_width);
          }
          $was_barcode = 1;
        }
        else {
          if ($was_barcode) {
            
            $sub_fragments = explode("<br />", $fragment, 2);
            
            $pdf->WriteHTML($sub_fragments[0], false);
            if (isset($sub_fragments[1])) {
              $actual_y = $pdf->getY();
              $pdf->setY($actual_y+0.8);
              $pdf->WriteHTML($sub_fragments[1], false);
            }
          }
          else {
            $pdf->WriteHTML($fragment, false);
          }
          $was_barcode = 0;
        }
      }
      $x = $x + $largeur_etiq;
      $pdf->SetY($y);
      $pdf->SetX($x);
    }
    
    if ($printer_id) {
      $file = new CFile;
      $file->_file_path = tempnam("/tmp", "etiq");
      file_put_contents($file->_file_path, $pdf->Output($this->nom.'.pdf', "S"));
      
      $printer = new CPrinter();
      $printer->load($printer_id);
      $printer->loadRefSource()->sendDocument($file);
      
      unlink($file->_file_path);
    }
    else if ($stream) {
      $pdf->Output($this->nom.'.pdf', "I");
    }
    else {
      return $pdf->OutPut($this->nom.'.pdf', "S");
    }
  }
}

CModeleEtiquette::$fields =
    array("CPatient"   => CPatient::$fields_etiq,
          "CSejour"    => CSejour::$fields_etiq,
          "COperation" => COperation::$fields_etiq,
          "General"    => array("DATE COURANTE", "HEURE COURANTE"));