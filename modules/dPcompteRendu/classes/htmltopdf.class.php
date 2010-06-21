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
  var $content = null;
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
      /*"hr",*/
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

  function __destruct() {
    $this->dompdf = null;
    unset($this->dompdf);
    $this->content = null;
    unset($this->content);
  }

  function generatePDF($content, $stream, $format, $orientation, $file) {
    $this->content = $this->fixBlockElements($content);
    $this->dompdf->set_paper($format, $orientation);
    $this->dompdf->set_protocol(isset($_SERVER["HTTPS"]) ? $protocol = "https://" : $protocol = "http://");
    $this->dompdf->set_host($_SERVER["SERVER_NAME"]);
    $this->dompdf->load_html($this->content);
    mbTrace($this->content,'',1);
    $this->dompdf->render();
    if($stream) {
      $this->dompdf->stream($file->file_name, array("Attachment" => 0));
    }
    else {
      file_put_contents($file->_file_path, $this->dompdf->output());
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
    $str = preg_replace("/<tr>\s*<\/tr>/", '', $str);
    $str = preg_replace("/<tr\/>/", '', $str);
    return $str;
  }

  function fixBlockElements($str) {

    $xml = new DOMDocument('1.0', 'iso-8859-1');

    $str = $this->xmlEntities($str);
    $str = $this->cleanWord($str);
    $str = $this->fix_latin1_mangled_with_utf8($str);

    $xml->loadXML($str);
    
    $html =& $xml->getElementsByTagName("body")->item(0);

    if ( is_null($html) )
      $html =& $xml->firstChild;

    if ( is_null($html) ) {
      CAppUI::stepAjax(CAppUI::tr("CCompteRendu-empty-doc"));
      CApp::rip();
    }

    $this->recursiveRemove($html);

    $str = $xml->saveHTML();
    $str = preg_replace("/<br>/", "<br/>", $str);
    return $str;
  }

  function recursiveRemove(DomNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }
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

  // Table extraite de :
  // - http://www.sourcerally.net/Scripts/39-Convert-HTML-Entities-to-XML-Entities
  // - http://yost.com/computers/htmlchars/html40charsbynumber.html
  function xmlEntities($str) {
    $xml =  array('&#34;'  , '&#38;'  , '&#60;'  , '&#62;'  , '&#160;' , '&#161;' , '&#162;' ,
                  '&#163;' , '&#164;' , '&#165;' , '&#166;' , '&#167;' , '&#168;' , '&#169;' ,
                  '&#170;' , '&#171;' , '&#172;' , '&#173;' , '&#174;' , '&#175;' , '&#176;' ,
                  '&#177;' , '&#178;' , '&#179;' , '&#180;' , '&#181;' , '&#182;' , '&#183;' ,
                  '&#184;' , '&#185;' , '&#186;' , '&#187;' , '&#188;' , '&#189;' , '&#190;' ,
                  '&#191;' , '&#192;' , '&#193;' , '&#194;' , '&#195;' , '&#196;' , '&#197;' ,
                  '&#198;' , '&#199;' , '&#200;' , '&#201;' , '&#202;' , '&#203;' , '&#204;' ,
                  '&#205;' , '&#206;' , '&#207;' , '&#208;' , '&#209;' , '&#210;' , '&#211;' ,
                  '&#212;' , '&#213;' , '&#214;' , '&#215;' , '&#216;' , '&#217;' , '&#218;' , 
                  '&#219;' , '&#220;' , '&#221;' , '&#222;' , '&#223;' , '&#224;' , '&#225;' ,
                  '&#226;' , '&#227;' , '&#228;' , '&#229;' , '&#230;' , '&#231;' , '&#232;' ,
                  '&#233;' , '&#234;' , '&#235;' , '&#236;' , '&#237;' , '&#238;' , '&#239;' ,
                  '&#240;' , '&#241;' , '&#242;' , '&#243;' , '&#244;' , '&#245;' , '&#246;' ,
                  '&#247;' , '&#248;' , '&#249;' , '&#250;' , '&#251;' , '&#252;' , '&#253;' ,
                  '&#254;' , '&#255;' , '&#338;' , '&#339;' ,
                  '&#8194;', '&#8195;', '&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8218;',
                  '&#8220;', '&#8221;', '&#8222;', '&#8226;' ,'&#8230;', '&#8240;', '&#8242;', '&#8243;', '&#8364;',
                  '&#8592;', '&#8593;', '&#8594;', '&#8595;', '&#8596;',
                  '&#8727;', '&#8804;', '&#8805;', '&#9674;', '&#9824;', '&#9827;', '&#9829;', '&#9830;');

    $html = array('&quot;'  , '&amp;'   , '&lt;'    , '&gt;'    , '&nbsp;'  , '&iexcl;' , '&cent;'  ,
                  '&pound; ', '&curren;', '&yen;'   , '&brvbar;', '&sect;'  , '&uml;'   , '&copy;'  ,
                  '&ordf;'  , '&laquo;' , '&not;'   , '&shy;'   , '&reg;'   , '&macr;'  , '&deg;'   ,
                  '&plusmn;', '&sup2;'  , '&sup3;'  , '&acute;' , '&micro;' , '&para;'  , '&middot;',
                  '&cedil;' , '&sup1;'  , '&ordm;'  , '&raquo;' , '&frac14;', '&frac12;', '&frac34;',
                  '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;' , '&Atilde;', '&Auml;'  , '&Aring;' ,
                  '&AElig;' ,' &Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;' , '&Euml;'  , '&Igrave;',
                  '&Iacute;',' &Icirc;' , '&Iuml;'  , '&ETH;'   , '&Ntilde;', '&Ograve;', '&Oacute;',
                  '&Ocirc;' , '&Otilde;', '&Ouml;'  , '&times;' , '&Oslash;', '&Ugrave;', '&Uacute;',
                  '&Ucirc;' , '&Uuml;'  , '&Yacute;', '&THORN;' , '&szlig;' , '&agrave;', '&aacute;',
                  '&acirc;' , '&atilde;', '&auml;'  , '&aring;' , '&aelig;' , '&ccedil;', '&egrave;',
                  '&eacute;', '&ecirc;' , '&euml;'  , '&igrave;', '&iacute;', '&icirc;' , '&iuml;'  ,
                  '&eth;'   , '&ntilde;', '&ograve;', '&oacute;', '&ocirc;' , '&otilde;', '&ouml;'  ,
                  '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;' , '&uuml;'  , '&yacute;',
                  '&thorn;' , '&yuml;'  , '&OElig;' , '&oelig;' ,
                  '&ensp;'  , '&emsp;'  , '&ndash;' , '&mdash;' , '&lsquo;' , '&rsquo;' , '&sbquo;' ,
                  '&ldquo;' , '&rdquo;' , '&bdquo;' , '&bull;'  , '&hellip;' ,'&permil;', '&prime;' , '&Prime;' , '&euro;'  ,
                  '&larr;'  , '&uarr;'  , '&rarr;'  , '&darr;'  , '&harr;'  ,
                  '&lowast;', '&le;'    , '&ge;'    , '&loz;'   , '&spades;', '&clubs;' , '&hearts;', '&diams;');
    $str = str_replace($html,$xml,$str);
    $str = str_ireplace($html,$xml,$str);
    return $str;
  }

  // Hack de caractères non utf8
  // http://stackoverflow.com/questions/2507608/error-input-is-not-proper-utf-8-indicate-encoding-using-phps-simplexml-loa
  function fix_latin1_mangled_with_utf8(&$str) {
    return preg_replace_callback(
      '#[\\xA0-\\xFF](?![\\x80-\\xBF]{2,})#',
      create_function('$m','return utf8_encode($m[0]);'), $str);
  }

  function html_validate() {
    $doc = new DOMDocument();
    return $doc->loadHTML($this->content) == 1;
  }
}
?>