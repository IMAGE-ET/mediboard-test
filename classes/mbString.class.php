<?php /* $Id: mbarray.class.php 7046 2009-10-13 12:29:24Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 7046 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

abstract class CMbString {
  const LOWERCASE = 1;
	const UPPERCASE = 2;
  const BOTHCASES = 3;
	
	static $glyphs = array (
    "a" => "אבגדהו",
    "c" => "ח",
    "e" => "טיךכ",
    "i" => "לםמן",
    "o" => "עףפץצר",
    "u" => "שתûü",
    "y" => "ÿ",
    "n" => "ס",
	);
	
	static $allographs = array (
	  "withdiacritics"    => "אבגדהועףפץצרטיךכחלםמןשתûüÿס",
    "withoutdiacritics" => "aaaaaaooooooeeeeciiiiuuuuyn",
	);
  
  /**
   * Remove diacritics from a string
   * @param string $string The string
   * @param constant $filter one of LOWERCASE, UPPERCASE or BOTHCASES (default)
   * @return string Result string
   **/
	static function removeDiacritics($string, $filter = self::BOTHCASES) {
	  $from = self::$allographs["withdiacritics"];
		$to   = self::$allographs["withoutdiacritics"];
		
		switch ($filter) {
			case self::LOWERCASE:
			break;
			
      case self::UPPERCASE:
      $from = strtoupper($from);
      $to   = strtoupper($to);
			break;
			
      default:
      case self::BOTHCASES:
      $from .= strtoupper($from);
      $to   .= strtoupper($to);
      break;
		}
			  
	  return strtr($string, $from, $to);
	}
	
  /**
   * Allow any kind of glyphs variants with diacritics in regular expression
   * @param string $regexp The regexp string
   * @return string Result regexp string
   **/
	static function allowDiacriticsInRegexp($regexp) {
		$regexp = self::removeDiacritics(strtolower($regexp));
		$fromto = array();
		foreach(self::$glyphs as $glyph => $allographs) {
			$fromto[$glyph] = "[$glyph$allographs]";
		}
		return strtr($regexp, $fromto);
	}
	
	/**
	 * Truncate a string to a given maximum length
	 * @param string $string The string to truncate
	 * @param int $max The max length of the resulting string, default to 25
	 * @param string $replacement The string that replaces the characters removed, default to '...'
	 * @return string The truncated string
	 */
	static function truncate($string, $max = 25, $replacement = '...'){
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
   * @param integer $number
   * @return string Deca-binary equivalent
   */
  static function toDecaBinary($number, $unit = "o") {
    $bytes = $value = $number;
    $suffix = "";
  
    $bytes = $bytes / 1024;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = "Ki";
    }
  
    $bytes = $bytes / 1024;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = "Mi";
    }
  
    $bytes = $bytes / 1024;
    if ($bytes >= 1) {
      $value = $bytes;
      $suffix = "Gi";
    }
    
    // Value with 3 significant digits
    $value = round($value, 2 - intval(log10($value)));
    return "$value$suffix$unit";
  }
	
  /**
   * Convert a deca-binary string to a integer
   * @param string $string Deca-binary string
   * @return integer Integer equivalent
   */
	static function fromDecaBinary($string, $unit = "o") {
	  $string = strtolower(trim($string));
	  if (!preg_match("/(.*)([kmgt])[$unit]?/", $string, $matches)) {
      return intval($string);
	  }
		
		list($string, $value, $suffix) = $matches;
	  switch($suffix) {
	    case 't': $value *= 1024;     
	    case 'g': $value *= 1024;
	    case 'm': $value *= 1024;
	    case 'k': $value *= 1024;
	  }
	  return intval($value);
	}
  
  static function unslash($str) {
    return strtr($str, array(
      "\\n" => "\n",
      "\\t" => "\t",
    ));
  }
  
  /**
   * Encodes HTML entities from a string
   * @param string $string The string to encode
   * @return 
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
    
    $string = htmlentities($string);
    return strtr($string, $ent);
  }
	
	/**
	 * Remove a token in the string
	 * @param string $string The string to reduce
	 * @param string $glue Implode/explode like glue
	 * @param string $token Token ton remove
	 * @return string
	 */
	static function removeToken($string, $glue, $token) {
		$tokens = explode($glue, $string);
		CMbArray::removeValue($token, $tokens);
		return implode($glue, $tokens);
	}
}
?>