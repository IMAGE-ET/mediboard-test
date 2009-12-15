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
	
	static $gliphs = array (
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
			
      case self::BOTHCASES:
      $from .= strtoupper($from);
      $to   .= strtoupper($to);
      break;

			default:
        trigger_error("Illegal filter '$filter'", E_USER_WARNING);
				return null;
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
		foreach(self::$gliphs as $gliph => $allographs) {
			$fromto[$gliph] = "[$gliph$allographs]";
		}
		$regexp = strtr($regexp, $fromto);
		return $regexp;
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

  /**
   * Convert a number to the deca-binary syntax
   * @param integer $number
   * @return string Deca-binary equivalent
   */
  static function toDecaBinary($number) {
    $bytes = $number;
    $value = $number;
    $suffix = "";
    $unit = "o";
  
    $kbytes = $bytes / 1024;
    if ($kbytes >= 1) {
      $value = $kbytes;
      $suffix = "Ki";
    }
  
    $mbytes = $kbytes / 1024;
    if ($mbytes >= 1) {
      $value = $mbytes;
      $suffix = "Mi";
    }
  
    $gbytes = $mbytes / 1024;
    if ($gbytes >= 1) {
      $value = $gbytes;
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
	function fromDecaBinary($string) {
    $unit = "o";
	  $string = strtolower(trim($string));
		$matches = array();
	  if (!preg_match("/(.*)([kmgt])[$unit]?/", $string, $matches)) {
      return intval($string);
	  };
		
		list($string, $value, $suffix) = $matches;
	  switch($suffix) {
	    case 't': $value *= 1024;     
	    case 'g': $value *= 1024;
	    case 'm': $value *= 1024;
	    case 'k': $value *= 1024;
	  }
	  return intval($value);
	}
	
}
?>