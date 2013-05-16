<?php
/**
 * $Id: CHtmlToPDF.class.php $
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

if (!is_dir("lib/dompdf")) {
  return;
}

/**
 * Conversion html vers pdf
 * Cette classe n'est pas un MbObject et les objets ne sont pas enregistrés en base
 */
class CHtmlToPDF {
  public $nbpages;
  public $content;

  public $display_elem = array (
    "inline" => array(
      "b", "strong", "big", "blink", "cite", "code", "del", "dfn",
      "em", "font", "i", "ins", "kbd", "nobr", "q", "s", "samp", "small",
      "span", "strike", "sub", "sup", "tt", "u", "var"
    ),
    "block"  => array(
      "address", "blockquote", "dd", "dl", "dt", "div", "dir",
      "h1", "h2", "h3", "h4", "h5", "h6", /*"hr",*/
      "listing", "isindex", "map", "menu", "multicol", "ol",
      "p", "pre", "plaintext", "table", "ul", "xmp",
    )
  );

  static $_width_page = 595.28;
  static $_marges = 2;
  
  static $_font_size_lookup = array(
    // For basefont support
    -3 => "4pt", 
    -2 => "5pt", 
    -1 => "6pt", 
     0 => "7pt", 
    
     1 => "8pt",
     2 => "10pt",
     3 => "12pt",
     4 => "14pt",
     5 => "18pt",
     6 => "24pt",
     7 => "34pt",
     
    // For basefont support
     8 => "48pt", 
     9 => "44pt", 
    10 => "52pt", 
    11 => "60pt", 
  );
  
  /**
   * Constructeur à partir d'une factory
   *
   * @param string $factory Factory name
   */
  function __construct($factory = null) {
    if (!$factory) {
      $factory = CAppUI::pref("choice_factory");
    }
    
    CHtmlToPDFConverter::init($factory);
  }
  
  /**
   * Destructeur standard
   */
  function __destruct() {
    $this->content = null;
    unset($this->content);
  }
  
  /**
   * Génération d'un pdf à partir d'une source, avec stream au client si demandé
   * 
   * @param string  $content     source html
   * @param boolean $stream      envoi du pdf au navigateur 
   * @param string  $format      format de la page
   * @param string  $orientation orientation de la page
   * @param CFile   $file        le CFile pour lequel générer le pdf
   * 
   * @return string
   */
  function generatePDF($content, $stream, $format, $orientation, $file) {
    $this->content = $this->fixBlockElements($content);
    $this->content = str_replace("[Général - numéro de page]", "<span class='page'></span>", $this->content);
    
    // Problème sous IE : supression de i'id sur la div
    
    $pdf_content = CHtmlToPDFConverter::convert($this->content, $format, $orientation);

    if ($file->_file_path) {
      file_put_contents($file->_file_path, $pdf_content);
    }
    
    $this->nbpages = preg_match_all("/\/Page\W/", $pdf_content, $matches);

    if ($stream) {
      header("Pragma: ");
      header("Cache-Control: ");
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
      header("Cache-Control: post-check=0, pre-check=0", false);
      // END extra headers to resolve IE caching bug
      header("MIME-Version: 1.0");
      header("Content-length: ".strlen($pdf_content));
      header('Content-type: application/pdf');
      header("Content-disposition: inline; filename=\"".$file->file_name."\"");
      
      echo $pdf_content;
    }
  }
  
  /**
   * Nettoyage de la source qui peut être altérée par un copier-coller provenant de word
   * Expressions régulières provenant de FCKEditor
   * cf http://docs.cksource.com/FCKeditor_2.x/Developers_Guide/Configuration/Configuration_Options/CleanWordKeepsStructure
   * 
   * @param string $str source html
   * 
   * @return string
   */
  static function cleanWord($str) {
    $str = str_replace("<o:p>", "<p>", $str);
    $str = str_replace("</o:p>", "</p>", $str);
    $str = str_replace("<w:", '<', $str);
    $str = str_replace("</w:", '</', $str);
    $str = preg_replace("/<o:smarttagtype.*smarttagtype>/", '', $str);
    $str = preg_replace("/<\/?\w+:[^>]*>/", '', $str);
    $str = preg_replace("/<tr>\s*<\/tr>/", '', $str);
    $str = str_replace("<tr/>", '', $str);
    $str = preg_replace("/<tr>[ \t\r\n\f]*<td>[ \t\r\n\f]*&#160;[ \t\r\n\f]*<\/td>[ \t\r\n\f]*<\/tr>/", '', $str);
    $str = str_replace("text-align:=\"\"", '', $str);
    $str = preg_replace("/v:shapes*=\"[_a-z0-9]+\"/", "", $str);
    return $str;
  }

  /**
   * Correction de problèmes de dom
   * 
   * @param string $str source html
   * 
   * @return string 
   */
  function fixBlockElements($str) {
    $xml = new DOMDocument('1.0', 'iso-8859-1');

    $str = CMbString::convertHTMLToXMLEntities($str);
    $str = CHtmlToPDF::cleanWord($str);
    
    // Suppression des caractères de contrôle
    $from = array(
      chr(3), // ETX (end of text)
      chr(7)  // BEL
    );

    $to = array(
      "",
      ""
    );

    $str = str_replace($from, $to, $str);

    $xml->loadXML(utf8_encode($str));
        
    $html =& $xml->getElementsByTagName("body")->item(0);

    if (is_null($html)) {
      $html =& $xml->firstChild;
    }
    if ( is_null($html) ) {
      CAppUI::stepAjax("CCompteRendu-empty-doc");
      CApp::rip();
    }
    
    $xpath = new DOMXpath($xml);

    $elements = $xpath->query("*/div[@id='body']");
    if (!is_null($elements)) {
      foreach ($elements as $_element) {
        CHtmlToPDF::removeAlign($_element);
      }
    }
    
    // Solution temporaire pour les problèmes de mise en page avec domPDF
    while ($elements = $xpath->query("//span[@class='field']")) {
      if ($elements->length == 0) {
        break;
      }
      foreach ($elements as $_element) {
        foreach ($_element->childNodes as $child) {
          $_element->parentNode->insertBefore($child->cloneNode(true), $_element);
        }
        $_element->parentNode->removeChild($_element);
      }
    }

    $this->recursiveRemove($html);
    $this->recursiveRemoveNestedFont($html);
    $this->resizeTable($html);

    $str = $xml->saveHTML();
    $str = preg_replace("/<br>/", "<br/>", $str);
    return $str;
  }
  
  /**
   * Correction récursive d'éléments de display inline qui imbriquent
   * des éléments de display block
   * 
   * @param DOMElement|DOMNode &$node noeud à parcourir
   * 
   * @return void
   */
  function recursiveRemove(DOMNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }
    foreach ($node->childNodes as $child) {
      if ((in_array($child->nodeName, $this->display_elem["block"]) &&
          in_array($node->nodeName, $this->display_elem["inline"])) ||
          ($node->nodeName == "span" && $child->nodeName == "hr")
      ) {
        // On force le display: block pour les éléments en display:inline et qui imbriquent des élements
        // en display: block.
        $style = $node->getAttribute("style");
        if (strpos($style, ";") != (strlen($style) - 1) && $style != "") {
          $style .= ";";
        }
        $node->setAttribute("style",  $style . "display: block;");
        break;
      }
      $this->recursiveRemove($child);
    }
  }
  
  /**
   * Transformation des tailles des tableaux de pixels en pourcentages
   * Feuille A4
   *   largeur en cm : 21
   *   largeur en pixels : 595.28
   *   
   * @param DOMElement|DOMNode &$node noeud à parcourir
   * 
   * @return void
   */
  function resizeTable(DOMNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }

    /** @var DOMElement $_child */
    foreach ($node->childNodes as $_child) {
      if ($_child->nodeName == "table") {
        $width = $_child->getAttribute("width");
        $width_without_marges = CHtmlToPDF::$_width_page - (CHtmlToPDF::$_marges / CHtmlToPDF::$_width_page) * 100;
        if (!strrpos($width, "%")) {
          if ($width > $width_without_marges) {
            $_child->setAttribute("width", "100%");
          }
          else if ($width <= $width_without_marges & $width > 0) {
            $new_width = ($width * 100) / ($width_without_marges - CHtmlToPDF::$_marges * 2 );
            $_child->setAttribute("width", "$new_width%");
          }
        }
      }
      CHtmlToPDF::resizeTable($_child);
    }
  }
  
  /**
   * Suppression d'attribut d'aligmement de tableau
   * 
   * @param DOMElement|DOMNode &$node noeud à parcourir
   * 
   * @return void
   */
  function removeAlign(DOMNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }

    /** @var DOMElement $_child */
    foreach ($node->childNodes as $_child) {
      if ($_child->nodeName == "table") {
        if ($_child->getAttribute("align") == "left" || $_child->getAttribute("align") == "right") {
          $_child->removeAttribute("align");
        }
      }
      CHtmlToPDF::removeAlign($_child);
    }
  }
  
  /**
   * Suppression des balises fonts imbriquées
   *  
   * @param DOMNode &$node noeud à parcourir
   * 
   * @return void
   */
  function recursiveRemoveNestedFont(DOMNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }

    foreach ($node->childNodes as $child) {
      if ($node->nodeName == "font" && $child->nodeName == "font" &&
          $node->firstChild && 
          $node->firstChild === $node->lastChild
      ) {
        if ($node->firstChild->getAttribute("family") == "") {
          $node->firstChild->setAttribute("family", $node->getAttribute("family"));
        }
        $child = $node->removeChild($node->firstChild);
        $parent = $node->parentNode;
        $parent->insertBefore($child, $node);
        $parent->removeChild($node);
      }
      CHtmlToPDF::recursiveRemoveNestedFont($child);
    }
  }
  
  /**
   * Validation d'une source html
   * 
   * @return boolean
   */
  function htmlValidate() {
    $doc = new DOMDocument();
    return $doc->loadHTML($this->content) == 1;
  }
}
