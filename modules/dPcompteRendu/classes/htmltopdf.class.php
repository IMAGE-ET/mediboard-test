<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

if (!is_dir("lib/dompdf")) return;

CAppUI::requireModuleFile("dPcompteRendu", "dompdf_config");
CAppUI::requireLibraryFile("dompdf/dompdf_config.inc");
CAppUI::requireLibraryFile("dompdf/include/dompdf.cls");

class CHtmlToPDF {

  var $nbpages = null;
  var $dompdf  = null;

  var $display_elem = array (
	  "inline" => array(
		  "b", "strong",
      "big",
      "blink",
      "cite",
      "code",
      "del",
      "dfn",
      "em",
      "font",
      "i",
      "ins",
      "kbd",
      "nobr",
      "q",
			"s",
			"samp",
      "small",
      "span",
			"strike",
      "sub",
      "sup",
			"tt",
			"u",
			"var"
      ),
	  "block"  => array(
		  "address",
      "blockquote",
      "dd",
      "dl",
      "dt",
      "div",
      "dir",
      "h1", "h2", "h3", "h4", "h5", "h6",
      "hr",
			"listing",
      "isindex",
      "map",
      "menu",
      "multicol",
      "ol",
			"p",
      "pre",
			"plaintext",
			"table",
			"ul",
			"xmp",
			));
      
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
  
  function __construct() {
    $this->dompdf = new DOMPDF;
  }

  function generatePDF($content, $stream, $format, $orientation, $path) {
    $content = $this->fixBlockElements($content);
		
    $this->dompdf->set_paper($format, $orientation);
    $this->dompdf->set_protocol(isset($_SERVER["HTTPS"]) ? $protocol = "https://" : $protocol = "http://");
    $this->dompdf->set_host($_SERVER["SERVER_NAME"]);
    $this->dompdf->load_html($content);
    $this->dompdf->render();

    if($stream) {
      $this->dompdf->stream("temp.pdf", array("Attachment" => 0));
    }
    else {
      file_put_contents($path, $this->dompdf->output());
      $this->nbpages = $this->dompdf->get_canvas()->get_page_count();
    }
  }

  // Expressions régulières provenant de FCKEditor
  // cf http://docs.cksource.com/FCKeditor_2.x/Developers_Guide/Configuration/Configuration_Options/CleanWordKeepsStructure
  function cleanWord($str) {
    $str = preg_replace("/<meta\s*[^>]*\s*[^\/]>/", '',$str);
    $str = preg_replace("/(<\/meta>)+/i", '', $str);
    $str = preg_replace("/<o:p><\/o:p>/", '', $str);
    $str = preg_replace("/<o:p>/", "<p>",$str);
    $str = preg_replace("/<\/o:p>/", "</p>",$str);
    $str = preg_replace("/<w:/", '<', $str);
    $str = preg_replace("/<\/w:/", '</', $str);
    $str = preg_replace("/<o:smarttagtype.*smarttagtype>/", '', $str);
    $str = preg_replace("/<\/?\w+:[^>]*>/", '', $str);
    return $str;
  }

  function fixBlockElements($str) {

    $xml = new DOMDocument();
    
    // Inspiré de http://www.php.net/manual/fr/function.get-html-translation-table.php#77660
    $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
    $trans[chr(130)] = '&sbquo;' ;    // Single Low-9 Quotation Mark
    $trans[chr(131)] = '&fnof;'  ;    // Latin Small Letter F With Hook
    $trans[chr(132)] = '&bdquo;' ;    // Double Low-9 Quotation Mark
    $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
    $trans[chr(134)] = '&dagger;';    // Dagger
    $trans[chr(135)] = '&Dagger;';    // Double Dagger
    $trans[chr(136)] = '&circ;'  ;    // Modifier Letter Circumflex Accent
    $trans[chr(137)] = '&permil;';    // Per Mille Sign
    $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
    $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
    $trans[chr(140)] = '&OElig;' ;    // Latin Capital Ligature OE
    $trans[chr(145)] = '&lsquo;' ;    // Left Single Quotation Mark
    $trans[chr(146)] = '&rsquo;' ;    // Right Single Quotation Mark
    $trans[chr(147)] = '&ldquo;' ;    // Left Double Quotation Mark
    $trans[chr(148)] = '&rdquo;' ;    // Right Double Quotation Mark
    $trans[chr(149)] = '&bull;'  ;    // Bullet
    $trans[chr(150)] = '&ndash;' ;    // En Dash
    $trans[chr(151)] = '&mdash;' ;    // Em Dash
    $trans[chr(152)] = '&tilde;' ;    // Small Tilde
    $trans[chr(153)] = '&trade;' ;    // Trade Mark Sign
    $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
    $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
    $trans[chr(156)] = '&oelig;' ;    // Latin Small Ligature OE
    $trans[chr(159)] = '&Yuml;'  ;    // Latin Capital Letter Y With Diaeresis
    $trans[chr(167)] = '&clubs;' ;    // Club Suit Symbol
    ksort($trans);
    
    foreach($trans as $a=>$b) {
      $str = str_replace($b, '&#'.ord($a).';', $str);
    }

    $str = $this->cleanWord($str);
    
    $xml->loadXML($str);
    
    $html =& $xml->getElementsByTagName("body")->item(0);

    if ( is_null($html) )
      $html =& $xml->firstChild;

    if ( is_null($html) ) {
      CAppUI::stepAjax(CAppUI::tr("CCompteRendu-empty-doc"));
    }

    $this->recursiveRemove($html);
    $str = $xml->saveXML();
    $str = str_replace('<?xml version="1.0"?>','',$str);
    return $str;
  }

  function recursiveRemove(DomNode &$node) {
    if ( !$node->hasChildNodes() )
      return;

    foreach($node->childNodes as $child) {
      if(in_array($child->nodeName, $this->display_elem["block"]) &&
         in_array($node->nodeName, $this->display_elem["inline"])) {
        
        foreach ( $node->attributes as $attr => $attr_node ) {
          $_attr = '';
          $_attr_value = '';
          switch($attr) {
            case "size":
              $_attr = "style";
              $_attr_value = $child->getAttribute($_attr)." font-size: ".CHtmlToPDF::$_font_size_lookup[$attr_node->value].';';
              break;
            case "face":
              $_attr = "style";
              $_attr_value = $child->getAttribute($_attr)." font-family: ".$attr_node->value.';';
              break;
            case "color":
              $_attr = "style";
              $_attr_value = $child->getAttribute($_attr)." color: ".$attr_node->value.';';
          }
          if($_attr != '' && $_attr_value != '') {
            $child->setAttribute($_attr, $_attr_value);
          }
        }
        $old_child = $child->parentNode->removeChild($child);
        $node->parentNode->insertBefore($old_child, $node);
      }
      $this->recursiveRemove($child);
    }
  }
}
?>