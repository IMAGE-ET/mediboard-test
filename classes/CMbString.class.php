<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

abstract class CMbString {
  const LOWERCASE = 1;
  const UPPERCASE = 2;
  const BOTHCASES = 3;

  static $glyphs = array (
    "a" => "àáâãäå",
    "c" => "ç",
    "e" => "èéêë",
    "i" => "ìíîï",
    "o" => "òóôõöø",
    "u" => "ùúûü",
    "y" => "ÿ",
    "n" => "ñ",
  );

  static $allographs = array (
    "withdiacritics"    => "àáâãäåòóôõöøèéêëçìíîïùúûüÿñ",
    "withoutdiacritics" => "aaaaaaooooooeeeeciiiiuuuuyn",
  );

  /**
   * Remove diacritics from a string
   *
   * @param string $string The string
   * @param int    $filter one of LOWERCASE, UPPERCASE or BOTHCASES (default)
   *
   * @return string Result string
   **/
  static function removeDiacritics($string, $filter = self::BOTHCASES) {
    $from = self::$allographs["withdiacritics"];
    $to   = self::$allographs["withoutdiacritics"];

    switch ($filter) {
      case self::LOWERCASE:
      break;

      case self::UPPERCASE:
      $from = mb_strtoupper($from);
      $to   = mb_strtoupper($to);
      break;

      default:
      case self::BOTHCASES:
      $from .= mb_strtoupper($from);
      $to   .= mb_strtoupper($to);
      break;
    }

    return strtr($string, $from, $to);
  }

  /**
   * Allow any kind of glyphs variants with diacritics in regular expression
   *
   * @param string $regexp The regexp string
   *
   * @return string Result regexp string
   **/
  static function allowDiacriticsInRegexp($regexp) {
    $regexp = self::removeDiacritics(strtolower($regexp));
    $fromto = array();
    foreach (self::$glyphs as $glyph => $allographs) {
      $fromto[$glyph] = "[$glyph$allographs]";
    }
    return strtr($regexp, $fromto);
  }

  /**
   * Truncate a string to a given maximum length
   *
   * @param string $string      The string to truncate
   * @param int    $max         The max length of the resulting string, default to 25
   * @param string $replacement The string that replaces the characters removed, default to '...'
   *
   * @return string The truncated string
   */
  static function truncate($string, $max = 25, $replacement = '...'){
    if (is_object($string)) {
      return $string;
    }

    if (strlen($string) > $max) {
      return substr($string, 0, $max - strlen($replacement)).$replacement;
    }
    return $string;
  }

  static function upper($string) {
    return mb_strtoupper($string, CApp::$encoding);
  }

  static function lower($string) {
    return mb_strtolower($string, CApp::$encoding);
  }

  static function capitalize($string) {
    return mb_ucwords($string);
  }

  /**
   * Convert a number to the deca-binary syntax
   *
   * @param integer $value Number
   * @param string  $unit  Unit
   *
   * @return string Deca-binary equivalent
   */
  static function toDecaBinary($value, $unit = "o") {
    return self::fromBytes($value, false)."i$unit";
  }

  /**
   * Convert a number to the deca-binary syntax
   *
   * @param integer $value Number
   * @param string  $unit  Unit
   *
   * @return string Deca-binary equivalent
   */
  static function toDecaSI($value, $unit = "o") {
    return self::fromBytes($value, true).$unit;
  }

  private static function fromBytes($value, $si = false) {
    $bytes = $value;
    $suffix = "";
    $ratio = ($si ? 1000 : 1024);

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = ($si ? "k" : "K");
    }

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = "M";
    }

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = "G";
    }

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = "T";
    }

    // Value with 3 significant digits
    $value = round($value, 2 - intval(log10($value)));
    return "$value$suffix";
  }

  private static function toBytes($string, $si = false) {
    $ratio = ($si ? 1000 : 1024);
    $string = strtolower(trim($string));

    if (!preg_match("/^([,\.\d]+)([kmgt])/", $string, $matches)) {
      return intval($string);
    }

    list($string, $value, $suffix) = $matches;

    switch ($suffix) {
      case 't': $value *= $ratio;     
      case 'g': $value *= $ratio;
      case 'm': $value *= $ratio;
      case 'k': $value *= $ratio;
    }

    return intval($value);
  }

  /**
   * Convert a deca-binary string to a integer
   *
   * @param string $string Deca-binary string
   *
   * @return integer Integer equivalent
   */
  static function fromDecaBinary($string) {
    return self::toBytes($string, false);
  }

  /**
   * Convert a deca-SI string to a integer
   *
   * @param string $string Deca-SI string
   *
   * @return integer Integer equivalent
   */
  static function fromDecaSI($string) {
    return self::toBytes($string, true);
  }

  static function unslash($str) {
    return strtr($str, array(
      "\\n" => "\n",
      "\\t" => "\t",
    ));
  }

  /**
   * Encodes HTML entities from a string
   *
   * @param string $string The string to encode
   *
   * @return string
   */
  static function htmlEncode($string) {
    // Strips MS Word entities
    $ent = array(
      chr(145) => '&#8216;',
      chr(146) => '&#8217;',
      chr(147) => '&#8220;',
      chr(148) => '&#8221;',
      chr(150) => '&#8211;',
      chr(151) => '&#8212;',
    );

    $string = CMbString::htmlEntities($string);
    return strtr($string, $ent);
  }

  /**
   * Equivalent to htmlspecialchars
   *
   * @param string $string        Input string
   * @param int    $flags         Flags
   * @param bool   $double_encode Enable double encode
   *
   * @return string
   */
  static function htmlSpecialChars($string, $flags = ENT_COMPAT, $double_encode = true) {
    return htmlspecialchars($string, $flags, CApp::$encoding, $double_encode);
  }

  /**
   * Equivalent to htmlentities
   *
   * @param string $string        Input string
   * @param int    $flags         Flags
   * @param bool   $double_encode Enable double encode
   *
   * @return string
   */
  static function htmlEntities($string, $flags = ENT_COMPAT, $double_encode = true) {
    return htmlentities($string, $flags, CApp::$encoding, $double_encode);
  }

  /**
   * Remove a token in the string
   *
   * @param string $string The string to reduce
   * @param string $glue   Implode/explode like glue
   * @param string $token  Token to remove
   *
   * @return string
   */
  static function removeToken($string, $glue, $token) {
    $tokens = explode($glue, $string);
    CMbArray::removeValue($token, $tokens);
    return implode($glue, $tokens);
  }

  static function isUTF8($string) {
    return mb_detect_encoding($string) === "UTF-8";

    /* // à tester :
    $invalidchars = '[\xC0-\xDF]([^\x80-\xBF]|$)' .
      '|[\xE0-\xEF].{0,1}([^\x80-\xBF]|$)' .
      '|[\xF0-\xF7].{0,2}([^\x80-\xBF]|$)' .
      '|[\xF8-\xFB].{0,3}([^\x80-\xBF]|$)' .
      '|[\xFC-\xFD].{0,4}([^\x80-\xBF]|$)' .
      '|[\xFE-\xFE].{0,5}([^\x80-\xBF]|$)' .
      '|[\x00-\x7F][\x80-\xBF]' .
      '|[\xC0-\xDF].[\x80-\xBF]' .
      '|[\xE0-\xEF]..[\x80-\xBF]' .
      '|[\xF0-\xF7]...[\x80-\xBF]' .
      '|[\xF8-\xFB]....[\x80-\xBF]' .
      '|[\xFC-\xFD].....[\x80-\xBF]' .
      '|[\xFE-\xFE]......[\x80-\xBF]' .
      '|^[\x80-\xBF]';

    return !preg_match("!$invalidchars!", $string);

    $length = strlen($string);

    for ($i=0; $i < $length; $i++) {
      $c = ord($string[$i]);
           if ($c < 0x80) $n = 0; # 0bbbbbbb
      elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
      elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
      elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
      elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
      elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
      else return false; # Does not match any model

      for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
        if ((++$i == $length) || ((ord($string[$i]) & 0xC0) != 0x80))
        return false;
      }
    }
    return true;*/
  }

  /**
   * Get a query string from params array. (reciproque parse_str)
   *
   * @param array $params Parameters
   *
   * @return string Query string
   */
  static function toQuery($params) {
    $_params = array();
    foreach ($params as $key => $value) {
      $_params[] = "$key=$value";
    }
    return implode("&", $_params);
  }

  /**
   * Turns HTML break tags to ascii new line
   * Reciproque for nl2br
   *
   * @param string $string HTML code
   *
   * @return string
   */
  static function br2nl($string) {
    // Actually just rmove break tag
    return str_ireplace("<br />", "", $string);
  }

  /**
   * Create hyperlinks around URLs in a string
   * 
   * @param string $str The string
   *
   * @return string The string with hyperlinks
   */
  static function makeUrlHyperlinks($str) {
    return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.#-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $str);
  }

  // Table extraite de :
  // - http://www.sourcerally.net/Scripts/39-Convert-HTML-Entities-to-XML-Entities
  // - http://yost.com/computers/htmlchars/html40charsbynumber.html
  static function convertHTMLToXMLEntities($str) {
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

    $str = str_replace($html, $xml, $str);
    $str = str_ireplace($html, $xml, $str);
    return $str;
  }

  static function highlightCode($language, $code, $enable_classes = true, $style = "max-height: 100%; white-space:pre-wrap;") {
    if (!class_exists("GeSHi", false)) {
      CAppUI::requireLibraryFile("geshi/geshi");
    }

    $geshi = new GeSHi($code, $language);
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style($style);
    $geshi->set_overall_class("geshi");

    if ($enable_classes) {
      $geshi->enable_classes();
    }

    return $geshi->parse_code();
  }

  static function toWords($num) {
    @list($whole, $decimal) = @preg_split('/[.,]/', $num);

    $nw = new nuts($whole, "");
    $words = $nw->convert("fr-FR");

    if ($decimal) {
      $nw = new nuts($decimal, "");
      $words .= " virgule ".$nw->convert("fr-FR");
    }

    return $words;
  }

  /**
   * Convert an HTML text to plain text.
   * Replace the <br> tags with '\n', and the html special chars by their equivalent in the chosen encoding
   *
   * @param string $html     The HTML to convert
   * @param string $encoding The encoding, default ISO-8859-1
   *
   * @return string
   */
  static function htmlToText($html, $encoding = "ISO-8859-1") {
    $text = str_replace("<br />", "\n", $html);
    $text = str_replace("&nbsp;", " ", $text);
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES, $encoding);
    $text = preg_replace('/[[:blank:]]{2,}/U', ' ', $text);
    $text = preg_replace('/(\n[[:blank:]]*){2,}/U', "\n", $text);
    return $text;
  }
}
