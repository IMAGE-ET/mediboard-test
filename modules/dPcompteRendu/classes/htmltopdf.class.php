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
    $this->dompdf->render();
    if($stream) {
      $this->dompdf->stream($file->file_name, array("Attachment" => 1));
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
    $str = str_replace("<o:p></o:p>", '', $str);
    $str = str_replace("<o:p>", "<p>",$str);
    $str = str_replace("</o:p>", "</p>",$str);
    $str = str_replace("<w:", '<', $str);
    $str = str_replace("</w:", '</', $str);
    $str = preg_replace("/<o:smarttagtype.*smarttagtype>/", '', $str);
    $str = preg_replace("/<\/?\w+:[^>]*>/", '', $str);
    $str = preg_replace("/<tr>\s*<\/tr>/", '', $str);
    $str = str_replace("<tr/>", '', $str);
    $str = str_replace("text-align:=\"\"", '', $str);
    return $str;
  }

  function fixBlockElements($str) {

    $xml = new DOMDocument('1.0', 'iso-8859-1');

    $str = $this->xmlEntities($str);
    $str = $this->cleanWord($str);

    $xml->loadXML(utf8_encode($str));
        
    $html =& $xml->getElementsByTagName("body")->item(0);

    if ( is_null($html) )
      $html =& $xml->firstChild;

    if ( is_null($html) ) {
      CAppUI::stepAjax(CAppUI::tr("CCompteRendu-empty-doc"));
      CApp::rip();
    }

    $this->recursiveRemove($html);
    $this->recursiveRemoveNestedFont($html);
    $str = $xml->saveHTML();
    $str = preg_replace("/<br>/", "<br/>", $str);
    return $str;
  }
  
  /*function stripEmptyTextNode {
    if (!$node->hasChildNodes()) {
      return;
    }
    foreach($node->childNodes)
  }*/

  function recursiveRemove(DomNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }
    foreach($node->childNodes as $child) {
      if(in_array($child->nodeName, $this->display_elem["block"]) &&
         in_array($node->nodeName, $this->display_elem["inline"])) {

         // On force le display: block pour les éléments en display:inline et qui imbriquent des élements
         // en display: block.
         $node->setAttribute("style", "display: block");

     /* if($node->nodeName == "span") {
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
        }*/
      }
      $this->recursiveRemove($child);
    }
  }

  function recursiveRemoveNestedFont(DomNode &$node) {
    if (!$node->hasChildNodes()) {
      return;
    }
    foreach($node->childNodes as $child) {
      if ($node->nodeName == "font" && $child->nodeName == "font" &&
          $node->firstChild && 
          $node->firstChild === $node->lastChild) {
         if ($node->firstChild->getAttribute("family") == ""){
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
  
  // Table extraite de :
  // - http://www.sourcerally.net/Scripts/39-Convert-HTML-Entities-to-XML-Entities
  // - http://yost.com/computers/htmlchars/html40charsbynumber.html
  function xmlEntities($str) {
    $xml =  array('&#34;'     , '&#38;'    , '&#60;'     , '&#62;'     , '&#160;'    , '&#161;'    , '&#162;' ,
                  '&#163;'    , '&#164;'   , '&#165;'    , '&#166;'    , '&#167;'    , '&#168;'    , '&#169;' ,
                  '&#170;'    , '&#171;'   , '&#172;'    , '&#173;'    , '&#174;'    , '&#175;'    , '&#176;' ,
                  '&#177;'    , '&#178;'   , '&#179;'    , '&#180;'    , '&#181;'    , '&#182;'    , '&#183;' ,
                  '&#184;'    , '&#185;'   , '&#186;'    , '&#187;'    , '&#188;'    , '&#189;'    , '&#190;' ,
                  '&#191;'    , '&#192;'   , '&#193;'    , '&#194;'    , '&#195;'    , '&#196;'    , '&#197;' ,
                  '&#198;'    , '&#199;'   , '&#200;'    , '&#201;'    , '&#202;'    , '&#203;'    , '&#204;' ,
                  '&#205;'    , '&#206;'   , '&#207;'    , '&#208;'    , '&#209;'    , '&#210;'    , '&#211;' ,
                  '&#212;'    , '&#213;'   , '&#214;'    , '&#215;'    , '&#216;'    , '&#217;'    , '&#218;' , 
                  '&#219;'    , '&#220;'   , '&#221;'    , '&#222;'    , '&#223;'    , '&#224;'    , '&#225;' ,
                  '&#226;'    , '&#227;'   , '&#228;'    , '&#229;'    , '&#230;'    , '&#231;'    , '&#232;' ,
                  '&#233;'    , '&#234;'   , '&#235;'    , '&#236;'    , '&#237;'    , '&#238;'    , '&#239;' ,
                  '&#240;'    , '&#241;'   , '&#242;'    , '&#243;'    , '&#244;'    , '&#245;'    , '&#246;' ,
                  '&#247;'    , '&#248;'   , '&#249;'    , '&#250;'    , '&#251;'    , '&#252;'    , '&#253;' ,
                  '&#254;'    , '&#255;'   , '&#338;'    , '&#339;'    , '&#352;'    , '&#353;'    , '&#376;' ,
                  '&#402;'    ,
                  '&#710;'    , '&#732;'   ,
                  '&#913;'    , '&#914;'   , '&#915;'    , '&#916;'    , '&#917;'    , '&#918;'    , '&#919;' ,
                  '&#920;'    , '&#921;'   , '&#922;'    , '&#923;'    , '&#924;'    , '&#925;'    , '&#926;' ,
                  '&#927;'    , '&#928;'   , '&#929;'    , '&#931;'    , '&#932;'    , '&#933;'    , '&#934;' ,
                  '&#935;'    , '&#936;'   , '&#937;'    , '&#945;'    , '&#946;'    , '&#947;'    , '&#948;' ,
                  '&#949;'    , '&#950;'   , '&#951;'    , '&#952;'    , '&#953;'    , '&#954;'    , '&#955;' ,
                  '&#956;'    , '&#957;'   , '&#958;'    , '&#959;'    , '&#960;'    , '&#961;'    , '&#962;' ,
                  '&#963;'    , '&#964;'   , '&#965;'    , '&#966;'    , '&#967;'    , '&#968;'    , '&#969;' ,
                  '&#977;'    , '&#978;'   , '&#982;'    ,
                  '&#8194;'   , '&#8195;'  , '&#8201;'   , '&#8204;'   , '&#8205;'   , '&#8206;'   , '&#8207;', 
                  '&#8211;'   , '&#8212;'  , '&#8216;'   , '&#8217;'   , '&#8218;'   ,
                  '&#8220;'   , '&#8221;'  , '&#8222;'   , '&#8224;'   , '&#8225;'   , '&#8226;'   , '&#8230;', 
                  '&#8240;'   , '&#8242;'  , '&#8243;'   , '&#8249;'   , '&#8250;'   , '&#8254;'   , '&#8260;',
                  '&#8364;'   ,
                  '&#8465;'   , '&#8472;'  , '&#8476;'   , '&#8482;'   ,
                  '&#8501;'   , '&#8592;'  , '&#8593;'   , '&#8594;'   , '&#8595;'   , '&#8596;'   ,
                  '&#8629;'   , '&#8656;'  , '&#8657;'   , '&#8658;'   , '&#8659;'   , '&#8660;'   ,
                  '&#8704;'   , '&#8706;'  , '&#8707;'   , '&#8709;'   , '&#8711;'   , '&#8712;'   , '&#8713;',
                  '&#8715;'   , '&#8719;'  , '&#8721;'   , '&#8722;'   , '&#8727;'   , '&#8730;'   , '&#8733;',
                  '&#8734;'   , '&#8736;'  , '&#8743;'   , '&#8744;'   , '&#8745;'   , '&#8746;'   , '&#8747;',
                  '&#8756;'   , '&#8764;'  , '&#8773;'   , '&#8776;'   , 
                  '&#8800;'   , '&#8801;'  , '&#8804;'   , '&#8805;'   , '&#8834;'   , '&#8835;'   , '&#8836;',
                  '&#8838;'   , '&#8839;'  , '&#8853;'   , '&#8855;'   , '&#8869;'   ,
                  '&#8901;'   , '&#8968;'  , '&#8969;'   , '&#8970;'   , '&#8971;'   ,
                  '&#9001'    , '&#9002;'  ,
                  '&#9674;'   , '&#9824;'  , '&#9827;'   , '&#9829;'   , '&#9830;'   );

    $html = array('&quot;'    , '&amp;'    , '&lt;'      , '&gt;'      , '&nbsp;'    , '&iexcl;'   , '&cent;'  ,
                  '&pound;'   , '&curren;' , '&yen;'     , '&brvbar;'  , '&sect;'    , '&uml;'     , '&copy;'  ,
                  '&ordf;'    , '&laquo;'  , '&not;'     , '&shy;'     , '&reg;'     , '&macr;'    , '&deg;'   ,
                  '&plusmn;'  , '&sup2;'   , '&sup3;'    , '&acute;'   , '&micro;'   , '&para;'    , '&middot;',
                  '&cedil;'   , '&sup1;'   , '&ordm;'    , '&raquo;'   , '&frac14;'  , '&frac12;'  , '&frac34;',
                  '&iquest;'  , '&Agrave;' , '&Aacute;'  , '&Acirc;'   , '&Atilde;'  , '&Auml;'    , '&Aring;' ,
                  '&AElig;'   ,' &Ccedil;' , '&Egrave;'  , '&Eacute;'  , '&Ecirc;'   , '&Euml;'    , '&Igrave;',
                  '&Iacute;'  ,' &Icirc;'  , '&Iuml;'    , '&ETH;'     , '&Ntilde;'  , '&Ograve;'  , '&Oacute;',
                  '&Ocirc;'   , '&Otilde;' , '&Ouml;'    , '&times;'   , '&Oslash;'  , '&Ugrave;'  , '&Uacute;',
                  '&Ucirc;'   , '&Uuml;'   , '&Yacute;'  , '&THORN;'   , '&szlig;'   , '&agrave;'  , '&aacute;',
                  '&acirc;'   , '&atilde;' , '&auml;'    , '&aring;'   , '&aelig;'   , '&ccedil;'  , '&egrave;',
                  '&eacute;'  , '&ecirc;'  , '&euml;'    , '&igrave;'  , '&iacute;'  , '&icirc;'   , '&iuml;'  ,
                  '&eth;'     , '&ntilde;' , '&ograve;'  , '&oacute;'  , '&ocirc;'   , '&otilde;'  , '&ouml;'  ,
                  '&divide;'  , '&oslash;' , '&ugrave;'  , '&uacute;'  , '&ucirc;'   , '&uuml;'    , '&yacute;',
                  '&thorn;'   , '&yuml;'   , '&OElig;'   , '&oelig;'   , '&Scaron;'  , '&scaron;'  , '&Yuml;'  ,
                  '&fnof;'    ,
                  '&circ;'    , '&tilde;'  ,
                  '&Alpha;'   , '&Beta;'   , '&Gamma;'   , '&Delta;'   , '&Epsilon;' , '&Zeta;'    , '&Eta;'   ,
                  '&Theta;'   , '&Iota;'   , '&Kappa;'   , '&Lambda;'  , '&Mu;'      , '&Nu;'      , '&Xi;'    ,
                  '&Omicron;' , '&Pi;'     , '&Rho;'     , '&Sigma;'   , '&Tau;'     , '&Upsilon;' , '&Phi;'   ,
                  '&Chi;'     , '&Psi;'    , '&Omega;'   , '&alpha;'   , '&beta;'    , '&gamma;'   , '&delta;' ,
                  '&epsilon;' , '&zeta;'   , '&eta;'     , '&theta;'   , '&iota;'    , '&kappa;'   , '&lambda;',
                  '&mu;'      , '&nu;'     , '&xi;'      , '&omicron;' , '&pi;'      , '&rho;'     , '&sigmaf;',
                  '&sigma;'   , '&tau;'    , '&upsilon;' , '&phi;'     , '&#chi;'    , '&psi;'     , '&omega;' ,
                  '&thetasym;', '&upsih;'  , '&piv;',
                  '&ensp;'    , '&emsp;'   , '&thinsp;'  , '&zwnj;'    , '&zwj;'     , '&lrm;'     , '&rlm;'   ,
                  '&ndash;'   , 
                  '&mdash;'   , '&lsquo;'  , '&rsquo;'   , '&sbquo;'   ,
                  '&ldquo;'   , '&rdquo;'  , '&bdquo;'   , '&dagger;'  , '&Dagger;'  , '&bull;'    , '&hellip;', 
                  '&permil;'  , '&prime;'  , '&Prime;'   , '&lsaquo;'  , '&rsaquo;'  , '&oline;'   , '&frasl;' ,
                  '&euro;'    ,
                  '&image;'   , '&weierp;' , '&real;'    , '&trade;'   ,
                  '&alefsym;' , '&larr;'   , '&uarr;'    , '&rarr;'    , '&darr;'    , '&harr;'    ,
                  '&crarr;'   , '&lArr;'   , '&uArr;'    , '&rArr;'    , '&dArr;'    , '&hArr;'    ,
                  '&forall;'  , '&part;'   , '&exist;'   , '&empty;'   , '&nabla;'   , '&isin;'    , '&notin;' ,
                  '&ni;'      , '&prod;'   , '&sum;'     , '&minus;'   , '&lowast;'  , '&radic;'   , '&prop;'  ,
                  '&infin;'   , '&ang;'    , '&and;'     , '&or;'      , '&cap;'     , '&cup;'     , '&int;'   ,
                  '&there4;'  , '&sim;'    , '&cong;'    , '&asymp;'   , 
                  '&ne;'      , '&equiv;'  , '&le;'      , '&ge;'      , '&sub;'     , '&sup;'     , '&nsub;'  ,
                  '&sube;'    , '&supe;'   , '&oplus;'   , '&otimes;'  , '&perp;'    ,
                  '&sdot;'    , '&lceil;'  , '&rceil;'   , '&lfloor;'  , '&rfloor;'  ,
                  '&lang;'    , '&rang;'   , 
                  '&loz;'     , '&spades;' , '&clubs;'   , '&hearts;'  , '&diams;'   );

    $str = str_replace($html,$xml,$str);
    $str = str_ireplace($html,$xml,$str);
    return $str;
  }

  function html_validate() {
    $doc = new DOMDocument();
    return $doc->loadHTML($this->content) == 1;
  }
}
?>