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

/**
 * Class for manipulate the chain
 */
abstract class CMbString {
  const LOWERCASE = 1;
  const UPPERCASE = 2;
  const BOTHCASES = 3;

  static $glyphs = array(
    "a" => "àáâãäå",
    "c" => "ç",
    "e" => "èéêë",
    "i" => "ìíîï",
    "o" => "òóôõöø",
    "u" => "ùúûü",
    "y" => "ÿ",
    "n" => "ñ",
  );

  static $allographs = array(
    "withdiacritics"    => "àáâãäåòóôõöøèéêëçìíîïùúûüÿñ",
    "withoutdiacritics" => "aaaaaaooooooeeeeciiiiuuuuyn",
  );

  static $diff;
  static $diff_html;

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
        $to .= mb_strtoupper($to);
        break;
    }

    return strtr($string, $from, $to);
  }

  /**
   * Return the string(UTF-8/ISO-8859-1) without accent
   *
   * @param String $string String
   *
   * @return string
   */
  static function removeAccents($string) {
    if (!preg_match('/[\x80-\xff]/', $string)) {
      return $string;
    }

    if (self::seemsUtf8($string)) {
      $chars = array(
        // Decompositions for Latin-1 Supplement
        chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
        chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
        chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
        chr(195) . chr(134) => 'AE', chr(195) . chr(135) => 'C',
        chr(195) . chr(136) => 'E', chr(195) . chr(137) => 'E',
        chr(195) . chr(138) => 'E', chr(195) . chr(139) => 'E',
        chr(195) . chr(140) => 'I', chr(195) . chr(141) => 'I',
        chr(195) . chr(142) => 'I', chr(195) . chr(143) => 'I',
        chr(195) . chr(144) => 'D', chr(195) . chr(145) => 'N',
        chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
        chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
        chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
        chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
        chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
        chr(195) . chr(159) => 'b', chr(195) . chr(160) => 'a',
        chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
        chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
        chr(195) . chr(165) => 'a', chr(195) . chr(166) => 'ae',
        chr(195) . chr(167) => 'c', chr(195) . chr(168) => 'e',
        chr(195) . chr(169) => 'e', chr(195) . chr(170) => 'e',
        chr(195) . chr(171) => 'e', chr(195) . chr(172) => 'i',
        chr(195) . chr(173) => 'i', chr(195) . chr(174) => 'i',
        chr(195) . chr(175) => 'i', chr(195) . chr(176) => 'd',
        chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
        chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
        chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
        chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
        chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
        chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
        chr(195) . chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
        chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
        chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
        chr(197) . chr(184) => 'Y', chr(197) . chr(189) => 'Z',
        chr(197) . chr(190) => 'z',);

      $string = strtr($string, $chars);
    }
    else {
      // Assume ISO-8859-1 if not UTF-8
      $chars['in'] = chr(138) . chr(142) . chr(154) . chr(158)
        . chr(159) . chr(192) . chr(193) . chr(194)
        . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
        . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
        . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
        . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
        . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
        . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
        . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
        . chr(252) . chr(253) . chr(255);

      $chars['out'] = "SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

      $string              = strtr($string, $chars['in'], $chars['out']);
      $double_chars['in']  = array(chr(140), chr(156), chr(198), chr(208), chr(223), chr(230), chr(240));
      $double_chars['out'] = array('OE', 'oe', 'AE', 'D', 'b', 'ae', 'd');
      $string              = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
  }

  /**
   * Check if the string is a UTF-8 String
   *
   * @param String $str String
   *
   * @return bool
   */
  static function seemsUtf8($str) {
    $length = strlen($str);
    for ($i = 0; $i < $length; $i++) {
      $c = ord($str[$i]);
      if ($c < 0x80) {
        $n = 0;
      } //0bbbbbbb
      elseif (($c & 0xE0) == 0xC0) {
        $n = 1;
      } //110bbbbb
      elseif (($c & 0xF0) == 0xE0) {
        $n = 2;
      } //1110bbbb
      elseif (($c & 0xF8) == 0xF0) {
        $n = 3;
      } //11110bbb
      elseif (($c & 0xFC) == 0xF8) {
        $n = 4;
      } //111110bb
      elseif (($c & 0xFE) == 0xFC) {
        $n = 5;
      } //1111110b
      else {
        return false;
      } //Does not match any model
      for ($j = 0; $j < $n; $j++) { //n bytes matching 10bbbbbb follow ?
        if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Replace the ban character with a escape
   *
   * @param String $string String to normalyze
   *
   * @return mixed
   */
  static function removeBanCharacter($string) {
    $String_no_accent = self::removeAccents($string);

    return preg_replace("/([^A-Za-z0-9])/", " ", $String_no_accent);
  }

  /**
   * Normalize a UTF-8 string
   * http://stackoverflow.com/a/7934397/92315
   *
   * @param string $string The UTF-8 string to normalize
   *
   * @return string
   */
  static function normalizeUtf8($string) {
    $conv = array(
      "A\xcc\x80"            => "\xc3\x80",
      "A\xcc\x81"            => "\xc3\x81",
      "A\xcc\x82"            => "\xc3\x82",
      "A\xcc\x83"            => "\xc3\x83",
      "A\xcc\x88"            => "\xc3\x84",
      "A\xcc\x8a"            => "\xc3\x85",
      "C\xcc\xa7"            => "\xc3\x87",
      "E\xcc\x80"            => "\xc3\x88",
      "E\xcc\x81"            => "\xc3\x89",
      "E\xcc\x82"            => "\xc3\x8a",
      "E\xcc\x88"            => "\xc3\x8b",
      "I\xcc\x80"            => "\xc3\x8c",
      "I\xcc\x81"            => "\xc3\x8d",
      "I\xcc\x82"            => "\xc3\x8e",
      "I\xcc\x88"            => "\xc3\x8f",
      "N\xcc\x83"            => "\xc3\x91",
      "O\xcc\x80"            => "\xc3\x92",
      "O\xcc\x81"            => "\xc3\x93",
      "O\xcc\x82"            => "\xc3\x94",
      "O\xcc\x83"            => "\xc3\x95",
      "O\xcc\x88"            => "\xc3\x96",
      "U\xcc\x80"            => "\xc3\x99",
      "U\xcc\x81"            => "\xc3\x9a",
      "U\xcc\x82"            => "\xc3\x9b",
      "U\xcc\x88"            => "\xc3\x9c",
      "Y\xcc\x81"            => "\xc3\x9d",
      "a\xcc\x80"            => "\xc3\xa0",
      "a\xcc\x81"            => "\xc3\xa1",
      "a\xcc\x82"            => "\xc3\xa2",
      "a\xcc\x83"            => "\xc3\xa3",
      "a\xcc\x88"            => "\xc3\xa4",
      "a\xcc\x8a"            => "\xc3\xa5",
      "c\xcc\xa7"            => "\xc3\xa7",
      "e\xcc\x80"            => "\xc3\xa8",
      "e\xcc\x81"            => "\xc3\xa9",
      "e\xcc\x82"            => "\xc3\xaa",
      "e\xcc\x88"            => "\xc3\xab",
      "i\xcc\x80"            => "\xc3\xac",
      "i\xcc\x81"            => "\xc3\xad",
      "i\xcc\x82"            => "\xc3\xae",
      "i\xcc\x88"            => "\xc3\xaf",
      "n\xcc\x83"            => "\xc3\xb1",
      "o\xcc\x80"            => "\xc3\xb2",
      "o\xcc\x81"            => "\xc3\xb3",
      "o\xcc\x82"            => "\xc3\xb4",
      "o\xcc\x83"            => "\xc3\xb5",
      "o\xcc\x88"            => "\xc3\xb6",
      "u\xcc\x80"            => "\xc3\xb9",
      "u\xcc\x81"            => "\xc3\xba",
      "u\xcc\x82"            => "\xc3\xbb",
      "u\xcc\x88"            => "\xc3\xbc",
      "y\xcc\x81"            => "\xc3\xbd",
      "y\xcc\x88"            => "\xc3\xbf",
      "A\xcc\x84"            => "\xc4\x80",
      "a\xcc\x84"            => "\xc4\x81",
      "A\xcc\x86"            => "\xc4\x82",
      "a\xcc\x86"            => "\xc4\x83",
      "A\xcc\xa8"            => "\xc4\x84",
      "a\xcc\xa8"            => "\xc4\x85",
      "C\xcc\x81"            => "\xc4\x86",
      "c\xcc\x81"            => "\xc4\x87",
      "C\xcc\x82"            => "\xc4\x88",
      "c\xcc\x82"            => "\xc4\x89",
      "C\xcc\x87"            => "\xc4\x8a",
      "c\xcc\x87"            => "\xc4\x8b",
      "C\xcc\x8c"            => "\xc4\x8c",
      "c\xcc\x8c"            => "\xc4\x8d",
      "D\xcc\x8c"            => "\xc4\x8e",
      "d\xcc\x8c"            => "\xc4\x8f",
      "E\xcc\x84"            => "\xc4\x92",
      "e\xcc\x84"            => "\xc4\x93",
      "E\xcc\x86"            => "\xc4\x94",
      "e\xcc\x86"            => "\xc4\x95",
      "E\xcc\x87"            => "\xc4\x96",
      "e\xcc\x87"            => "\xc4\x97",
      "E\xcc\xa8"            => "\xc4\x98",
      "e\xcc\xa8"            => "\xc4\x99",
      "E\xcc\x8c"            => "\xc4\x9a",
      "e\xcc\x8c"            => "\xc4\x9b",
      "G\xcc\x82"            => "\xc4\x9c",
      "g\xcc\x82"            => "\xc4\x9d",
      "G\xcc\x86"            => "\xc4\x9e",
      "g\xcc\x86"            => "\xc4\x9f",
      "G\xcc\x87"            => "\xc4\xa0",
      "g\xcc\x87"            => "\xc4\xa1",
      "G\xcc\xa7"            => "\xc4\xa2",
      "g\xcc\xa7"            => "\xc4\xa3",
      "H\xcc\x82"            => "\xc4\xa4",
      "h\xcc\x82"            => "\xc4\xa5",
      "I\xcc\x83"            => "\xc4\xa8",
      "i\xcc\x83"            => "\xc4\xa9",
      "I\xcc\x84"            => "\xc4\xaa",
      "i\xcc\x84"            => "\xc4\xab",
      "I\xcc\x86"            => "\xc4\xac",
      "i\xcc\x86"            => "\xc4\xad",
      "I\xcc\xa8"            => "\xc4\xae",
      "i\xcc\xa8"            => "\xc4\xaf",
      "I\xcc\x87"            => "\xc4\xb0",
      "J\xcc\x82"            => "\xc4\xb4",
      "j\xcc\x82"            => "\xc4\xb5",
      "K\xcc\xa7"            => "\xc4\xb6",
      "k\xcc\xa7"            => "\xc4\xb7",
      "L\xcc\x81"            => "\xc4\xb9",
      "l\xcc\x81"            => "\xc4\xba",
      "L\xcc\xa7"            => "\xc4\xbb",
      "l\xcc\xa7"            => "\xc4\xbc",
      "L\xcc\x8c"            => "\xc4\xbd",
      "l\xcc\x8c"            => "\xc4\xbe",
      "N\xcc\x81"            => "\xc5\x83",
      "n\xcc\x81"            => "\xc5\x84",
      "N\xcc\xa7"            => "\xc5\x85",
      "n\xcc\xa7"            => "\xc5\x86",
      "N\xcc\x8c"            => "\xc5\x87",
      "n\xcc\x8c"            => "\xc5\x88",
      "O\xcc\x84"            => "\xc5\x8c",
      "o\xcc\x84"            => "\xc5\x8d",
      "O\xcc\x86"            => "\xc5\x8e",
      "o\xcc\x86"            => "\xc5\x8f",
      "O\xcc\x8b"            => "\xc5\x90",
      "o\xcc\x8b"            => "\xc5\x91",
      "R\xcc\x81"            => "\xc5\x94",
      "r\xcc\x81"            => "\xc5\x95",
      "R\xcc\xa7"            => "\xc5\x96",
      "r\xcc\xa7"            => "\xc5\x97",
      "R\xcc\x8c"            => "\xc5\x98",
      "r\xcc\x8c"            => "\xc5\x99",
      "S\xcc\x81"            => "\xc5\x9a",
      "s\xcc\x81"            => "\xc5\x9b",
      "S\xcc\x82"            => "\xc5\x9c",
      "s\xcc\x82"            => "\xc5\x9d",
      "S\xcc\xa7"            => "\xc5\x9e",
      "s\xcc\xa7"            => "\xc5\x9f",
      "S\xcc\x8c"            => "\xc5\xa0",
      "s\xcc\x8c"            => "\xc5\xa1",
      "T\xcc\xa7"            => "\xc5\xa2",
      "t\xcc\xa7"            => "\xc5\xa3",
      "T\xcc\x8c"            => "\xc5\xa4",
      "t\xcc\x8c"            => "\xc5\xa5",
      "U\xcc\x83"            => "\xc5\xa8",
      "u\xcc\x83"            => "\xc5\xa9",
      "U\xcc\x84"            => "\xc5\xaa",
      "u\xcc\x84"            => "\xc5\xab",
      "U\xcc\x86"            => "\xc5\xac",
      "u\xcc\x86"            => "\xc5\xad",
      "U\xcc\x8a"            => "\xc5\xae",
      "u\xcc\x8a"            => "\xc5\xaf",
      "U\xcc\x8b"            => "\xc5\xb0",
      "u\xcc\x8b"            => "\xc5\xb1",
      "U\xcc\xa8"            => "\xc5\xb2",
      "u\xcc\xa8"            => "\xc5\xb3",
      "W\xcc\x82"            => "\xc5\xb4",
      "w\xcc\x82"            => "\xc5\xb5",
      "Y\xcc\x82"            => "\xc5\xb6",
      "y\xcc\x82"            => "\xc5\xb7",
      "Y\xcc\x88"            => "\xc5\xb8",
      "Z\xcc\x81"            => "\xc5\xb9",
      "z\xcc\x81"            => "\xc5\xba",
      "Z\xcc\x87"            => "\xc5\xbb",
      "z\xcc\x87"            => "\xc5\xbc",
      "Z\xcc\x8c"            => "\xc5\xbd",
      "z\xcc\x8c"            => "\xc5\xbe",
      "O\xcc\x9b"            => "\xc6\xa0",
      "o\xcc\x9b"            => "\xc6\xa1",
      "U\xcc\x9b"            => "\xc6\xaf",
      "u\xcc\x9b"            => "\xc6\xb0",
      "A\xcc\x8c"            => "\xc7\x8d",
      "a\xcc\x8c"            => "\xc7\x8e",
      "I\xcc\x8c"            => "\xc7\x8f",
      "i\xcc\x8c"            => "\xc7\x90",
      "O\xcc\x8c"            => "\xc7\x91",
      "o\xcc\x8c"            => "\xc7\x92",
      "U\xcc\x8c"            => "\xc7\x93",
      "u\xcc\x8c"            => "\xc7\x94",
      "\xc3\x9c\xcc\x84"     => "\xc7\x95",
      "\xc3\xbc\xcc\x84"     => "\xc7\x96",
      "\xc3\x9c\xcc\x81"     => "\xc7\x97",
      "\xc3\xbc\xcc\x81"     => "\xc7\x98",
      "\xc3\x9c\xcc\x8c"     => "\xc7\x99",
      "\xc3\xbc\xcc\x8c"     => "\xc7\x9a",
      "\xc3\x9c\xcc\x80"     => "\xc7\x9b",
      "\xc3\xbc\xcc\x80"     => "\xc7\x9c",
      "\xc3\x84\xcc\x84"     => "\xc7\x9e",
      "\xc3\xa4\xcc\x84"     => "\xc7\x9f",
      "\xc8\xa6\xcc\x84"     => "\xc7\xa0",
      "\xc8\xa7\xcc\x84"     => "\xc7\xa1",
      "\xc3\x86\xcc\x84"     => "\xc7\xa2",
      "\xc3\xa6\xcc\x84"     => "\xc7\xa3",
      "G\xcc\x8c"            => "\xc7\xa6",
      "g\xcc\x8c"            => "\xc7\xa7",
      "K\xcc\x8c"            => "\xc7\xa8",
      "k\xcc\x8c"            => "\xc7\xa9",
      "O\xcc\xa8"            => "\xc7\xaa",
      "o\xcc\xa8"            => "\xc7\xab",
      "\xc7\xaa\xcc\x84"     => "\xc7\xac",
      "\xc7\xab\xcc\x84"     => "\xc7\xad",
      "\xc6\xb7\xcc\x8c"     => "\xc7\xae",
      "\xca\x92\xcc\x8c"     => "\xc7\xaf",
      "j\xcc\x8c"            => "\xc7\xb0",
      "G\xcc\x81"            => "\xc7\xb4",
      "g\xcc\x81"            => "\xc7\xb5",
      "N\xcc\x80"            => "\xc7\xb8",
      "n\xcc\x80"            => "\xc7\xb9",
      "\xc3\x85\xcc\x81"     => "\xc7\xba",
      "\xc3\xa5\xcc\x81"     => "\xc7\xbb",
      "\xc3\x86\xcc\x81"     => "\xc7\xbc",
      "\xc3\xa6\xcc\x81"     => "\xc7\xbd",
      "\xc3\x98\xcc\x81"     => "\xc7\xbe",
      "\xc3\xb8\xcc\x81"     => "\xc7\xbf",
      "A\xcc\x8f"            => "\xc8\x80",
      "a\xcc\x8f"            => "\xc8\x81",
      "A\xcc\x91"            => "\xc8\x82",
      "a\xcc\x91"            => "\xc8\x83",
      "E\xcc\x8f"            => "\xc8\x84",
      "e\xcc\x8f"            => "\xc8\x85",
      "E\xcc\x91"            => "\xc8\x86",
      "e\xcc\x91"            => "\xc8\x87",
      "I\xcc\x8f"            => "\xc8\x88",
      "i\xcc\x8f"            => "\xc8\x89",
      "I\xcc\x91"            => "\xc8\x8a",
      "i\xcc\x91"            => "\xc8\x8b",
      "O\xcc\x8f"            => "\xc8\x8c",
      "o\xcc\x8f"            => "\xc8\x8d",
      "O\xcc\x91"            => "\xc8\x8e",
      "o\xcc\x91"            => "\xc8\x8f",
      "R\xcc\x8f"            => "\xc8\x90",
      "r\xcc\x8f"            => "\xc8\x91",
      "R\xcc\x91"            => "\xc8\x92",
      "r\xcc\x91"            => "\xc8\x93",
      "U\xcc\x8f"            => "\xc8\x94",
      "u\xcc\x8f"            => "\xc8\x95",
      "U\xcc\x91"            => "\xc8\x96",
      "u\xcc\x91"            => "\xc8\x97",
      "S\xcc\xa6"            => "\xc8\x98",
      "s\xcc\xa6"            => "\xc8\x99",
      "T\xcc\xa6"            => "\xc8\x9a",
      "t\xcc\xa6"            => "\xc8\x9b",
      "H\xcc\x8c"            => "\xc8\x9e",
      "h\xcc\x8c"            => "\xc8\x9f",
      "A\xcc\x87"            => "\xc8\xa6",
      "a\xcc\x87"            => "\xc8\xa7",
      "E\xcc\xa7"            => "\xc8\xa8",
      "e\xcc\xa7"            => "\xc8\xa9",
      "\xc3\x96\xcc\x84"     => "\xc8\xaa",
      "\xc3\xb6\xcc\x84"     => "\xc8\xab",
      "\xc3\x95\xcc\x84"     => "\xc8\xac",
      "\xc3\xb5\xcc\x84"     => "\xc8\xad",
      "O\xcc\x87"            => "\xc8\xae",
      "o\xcc\x87"            => "\xc8\xaf",
      "\xc8\xae\xcc\x84"     => "\xc8\xb0",
      "\xc8\xaf\xcc\x84"     => "\xc8\xb1",
      "Y\xcc\x84"            => "\xc8\xb2",
      "y\xcc\x84"            => "\xc8\xb3",
      "\xcc\x88\xcc\x81"     => "\xcd\x84",
      "\xc2\xa8\xcc\x81"     => "\xce\x85",
      "\xce\x91\xcc\x81"     => "\xce\x86",
      "\xce\x95\xcc\x81"     => "\xce\x88",
      "\xce\x97\xcc\x81"     => "\xce\x89",
      "\xce\x99\xcc\x81"     => "\xce\x8a",
      "\xce\x9f\xcc\x81"     => "\xce\x8c",
      "\xce\xa5\xcc\x81"     => "\xce\x8e",
      "\xce\xa9\xcc\x81"     => "\xce\x8f",
      "\xcf\x8a\xcc\x81"     => "\xce\x90",
      "\xce\x99\xcc\x88"     => "\xce\xaa",
      "\xce\xa5\xcc\x88"     => "\xce\xab",
      "\xce\xb1\xcc\x81"     => "\xce\xac",
      "\xce\xb5\xcc\x81"     => "\xce\xad",
      "\xce\xb7\xcc\x81"     => "\xce\xae",
      "\xce\xb9\xcc\x81"     => "\xce\xaf",
      "\xcf\x8b\xcc\x81"     => "\xce\xb0",
      "\xce\xb9\xcc\x88"     => "\xcf\x8a",
      "\xcf\x85\xcc\x88"     => "\xcf\x8b",
      "\xce\xbf\xcc\x81"     => "\xcf\x8c",
      "\xcf\x85\xcc\x81"     => "\xcf\x8d",
      "\xcf\x89\xcc\x81"     => "\xcf\x8e",
      "\xcf\x92\xcc\x81"     => "\xcf\x93",
      "\xcf\x92\xcc\x88"     => "\xcf\x94",
      "\xd0\x95\xcc\x80"     => "\xd0\x80",
      "\xd0\x95\xcc\x88"     => "\xd0\x81",
      "\xd0\x93\xcc\x81"     => "\xd0\x83",
      "\xd0\x86\xcc\x88"     => "\xd0\x87",
      "\xd0\x9a\xcc\x81"     => "\xd0\x8c",
      "\xd0\x98\xcc\x80"     => "\xd0\x8d",
      "\xd0\xa3\xcc\x86"     => "\xd0\x8e",
      "\xd0\x98\xcc\x86"     => "\xd0\x99",
      "\xd0\xb8\xcc\x86"     => "\xd0\xb9",
      "\xd0\xb5\xcc\x80"     => "\xd1\x90",
      "\xd0\xb5\xcc\x88"     => "\xd1\x91",
      "\xd0\xb3\xcc\x81"     => "\xd1\x93",
      "\xd1\x96\xcc\x88"     => "\xd1\x97",
      "\xd0\xba\xcc\x81"     => "\xd1\x9c",
      "\xd0\xb8\xcc\x80"     => "\xd1\x9d",
      "\xd1\x83\xcc\x86"     => "\xd1\x9e",
      "\xd1\xb4\xcc\x8f"     => "\xd1\xb6",
      "\xd1\xb5\xcc\x8f"     => "\xd1\xb7",
      "\xd0\x96\xcc\x86"     => "\xd3\x81",
      "\xd0\xb6\xcc\x86"     => "\xd3\x82",
      "\xd0\x90\xcc\x86"     => "\xd3\x90",
      "\xd0\xb0\xcc\x86"     => "\xd3\x91",
      "\xd0\x90\xcc\x88"     => "\xd3\x92",
      "\xd0\xb0\xcc\x88"     => "\xd3\x93",
      "\xd0\x95\xcc\x86"     => "\xd3\x96",
      "\xd0\xb5\xcc\x86"     => "\xd3\x97",
      "\xd3\x98\xcc\x88"     => "\xd3\x9a",
      "\xd3\x99\xcc\x88"     => "\xd3\x9b",
      "\xd0\x96\xcc\x88"     => "\xd3\x9c",
      "\xd0\xb6\xcc\x88"     => "\xd3\x9d",
      "\xd0\x97\xcc\x88"     => "\xd3\x9e",
      "\xd0\xb7\xcc\x88"     => "\xd3\x9f",
      "\xd0\x98\xcc\x84"     => "\xd3\xa2",
      "\xd0\xb8\xcc\x84"     => "\xd3\xa3",
      "\xd0\x98\xcc\x88"     => "\xd3\xa4",
      "\xd0\xb8\xcc\x88"     => "\xd3\xa5",
      "\xd0\x9e\xcc\x88"     => "\xd3\xa6",
      "\xd0\xbe\xcc\x88"     => "\xd3\xa7",
      "\xd3\xa8\xcc\x88"     => "\xd3\xaa",
      "\xd3\xa9\xcc\x88"     => "\xd3\xab",
      "\xd0\xad\xcc\x88"     => "\xd3\xac",
      "\xd1\x8d\xcc\x88"     => "\xd3\xad",
      "\xd0\xa3\xcc\x84"     => "\xd3\xae",
      "\xd1\x83\xcc\x84"     => "\xd3\xaf",
      "\xd0\xa3\xcc\x88"     => "\xd3\xb0",
      "\xd1\x83\xcc\x88"     => "\xd3\xb1",
      "\xd0\xa3\xcc\x8b"     => "\xd3\xb2",
      "\xd1\x83\xcc\x8b"     => "\xd3\xb3",
      "\xd0\xa7\xcc\x88"     => "\xd3\xb4",
      "\xd1\x87\xcc\x88"     => "\xd3\xb5",
      "\xd0\xab\xcc\x88"     => "\xd3\xb8",
      "\xd1\x8b\xcc\x88"     => "\xd3\xb9",
      "A\xcc\xa5"            => "\xe1\xb8\x80",
      "a\xcc\xa5"            => "\xe1\xb8\x81",
      "B\xcc\x87"            => "\xe1\xb8\x82",
      "b\xcc\x87"            => "\xe1\xb8\x83",
      "B\xcc\xa3"            => "\xe1\xb8\x84",
      "b\xcc\xa3"            => "\xe1\xb8\x85",
      "B\xcc\xb1"            => "\xe1\xb8\x86",
      "b\xcc\xb1"            => "\xe1\xb8\x87",
      "\xc3\x87\xcc\x81"     => "\xe1\xb8\x88",
      "\xc3\xa7\xcc\x81"     => "\xe1\xb8\x89",
      "D\xcc\x87"            => "\xe1\xb8\x8a",
      "d\xcc\x87"            => "\xe1\xb8\x8b",
      "D\xcc\xa3"            => "\xe1\xb8\x8c",
      "d\xcc\xa3"            => "\xe1\xb8\x8d",
      "D\xcc\xb1"            => "\xe1\xb8\x8e",
      "d\xcc\xb1"            => "\xe1\xb8\x8f",
      "D\xcc\xa7"            => "\xe1\xb8\x90",
      "d\xcc\xa7"            => "\xe1\xb8\x91",
      "D\xcc\xad"            => "\xe1\xb8\x92",
      "d\xcc\xad"            => "\xe1\xb8\x93",
      "\xc4\x92\xcc\x80"     => "\xe1\xb8\x94",
      "\xc4\x93\xcc\x80"     => "\xe1\xb8\x95",
      "\xc4\x92\xcc\x81"     => "\xe1\xb8\x96",
      "\xc4\x93\xcc\x81"     => "\xe1\xb8\x97",
      "E\xcc\xad"            => "\xe1\xb8\x98",
      "e\xcc\xad"            => "\xe1\xb8\x99",
      "E\xcc\xb0"            => "\xe1\xb8\x9a",
      "e\xcc\xb0"            => "\xe1\xb8\x9b",
      "\xc8\xa8\xcc\x86"     => "\xe1\xb8\x9c",
      "\xc8\xa9\xcc\x86"     => "\xe1\xb8\x9d",
      "F\xcc\x87"            => "\xe1\xb8\x9e",
      "f\xcc\x87"            => "\xe1\xb8\x9f",
      "G\xcc\x84"            => "\xe1\xb8\xa0",
      "g\xcc\x84"            => "\xe1\xb8\xa1",
      "H\xcc\x87"            => "\xe1\xb8\xa2",
      "h\xcc\x87"            => "\xe1\xb8\xa3",
      "H\xcc\xa3"            => "\xe1\xb8\xa4",
      "h\xcc\xa3"            => "\xe1\xb8\xa5",
      "H\xcc\x88"            => "\xe1\xb8\xa6",
      "h\xcc\x88"            => "\xe1\xb8\xa7",
      "H\xcc\xa7"            => "\xe1\xb8\xa8",
      "h\xcc\xa7"            => "\xe1\xb8\xa9",
      "H\xcc\xae"            => "\xe1\xb8\xaa",
      "h\xcc\xae"            => "\xe1\xb8\xab",
      "I\xcc\xb0"            => "\xe1\xb8\xac",
      "i\xcc\xb0"            => "\xe1\xb8\xad",
      "\xc3\x8f\xcc\x81"     => "\xe1\xb8\xae",
      "\xc3\xaf\xcc\x81"     => "\xe1\xb8\xaf",
      "K\xcc\x81"            => "\xe1\xb8\xb0",
      "k\xcc\x81"            => "\xe1\xb8\xb1",
      "K\xcc\xa3"            => "\xe1\xb8\xb2",
      "k\xcc\xa3"            => "\xe1\xb8\xb3",
      "K\xcc\xb1"            => "\xe1\xb8\xb4",
      "k\xcc\xb1"            => "\xe1\xb8\xb5",
      "L\xcc\xa3"            => "\xe1\xb8\xb6",
      "l\xcc\xa3"            => "\xe1\xb8\xb7",
      "\xe1\xb8\xb6\xcc\x84" => "\xe1\xb8\xb8",
      "\xe1\xb8\xb7\xcc\x84" => "\xe1\xb8\xb9",
      "L\xcc\xb1"            => "\xe1\xb8\xba",
      "l\xcc\xb1"            => "\xe1\xb8\xbb",
      "L\xcc\xad"            => "\xe1\xb8\xbc",
      "l\xcc\xad"            => "\xe1\xb8\xbd",
      "M\xcc\x81"            => "\xe1\xb8\xbe",
      "m\xcc\x81"            => "\xe1\xb8\xbf",
      "M\xcc\x87"            => "\xe1\xb9\x80",
      "m\xcc\x87"            => "\xe1\xb9\x81",
      "M\xcc\xa3"            => "\xe1\xb9\x82",
      "m\xcc\xa3"            => "\xe1\xb9\x83",
      "N\xcc\x87"            => "\xe1\xb9\x84",
      "n\xcc\x87"            => "\xe1\xb9\x85",
      "N\xcc\xa3"            => "\xe1\xb9\x86",
      "n\xcc\xa3"            => "\xe1\xb9\x87",
      "N\xcc\xb1"            => "\xe1\xb9\x88",
      "n\xcc\xb1"            => "\xe1\xb9\x89",
      "N\xcc\xad"            => "\xe1\xb9\x8a",
      "n\xcc\xad"            => "\xe1\xb9\x8b",
      "\xc3\x95\xcc\x81"     => "\xe1\xb9\x8c",
      "\xc3\xb5\xcc\x81"     => "\xe1\xb9\x8d",
      "\xc3\x95\xcc\x88"     => "\xe1\xb9\x8e",
      "\xc3\xb5\xcc\x88"     => "\xe1\xb9\x8f",
      "\xc5\x8c\xcc\x80"     => "\xe1\xb9\x90",
      "\xc5\x8d\xcc\x80"     => "\xe1\xb9\x91",
      "\xc5\x8c\xcc\x81"     => "\xe1\xb9\x92",
      "\xc5\x8d\xcc\x81"     => "\xe1\xb9\x93",
      "P\xcc\x81"            => "\xe1\xb9\x94",
      "p\xcc\x81"            => "\xe1\xb9\x95",
      "P\xcc\x87"            => "\xe1\xb9\x96",
      "p\xcc\x87"            => "\xe1\xb9\x97",
      "R\xcc\x87"            => "\xe1\xb9\x98",
      "r\xcc\x87"            => "\xe1\xb9\x99",
      "R\xcc\xa3"            => "\xe1\xb9\x9a",
      "r\xcc\xa3"            => "\xe1\xb9\x9b",
      "\xe1\xb9\x9a\xcc\x84" => "\xe1\xb9\x9c",
      "\xe1\xb9\x9b\xcc\x84" => "\xe1\xb9\x9d",
      "R\xcc\xb1"            => "\xe1\xb9\x9e",
      "r\xcc\xb1"            => "\xe1\xb9\x9f",
      "S\xcc\x87"            => "\xe1\xb9\xa0",
      "s\xcc\x87"            => "\xe1\xb9\xa1",
      "S\xcc\xa3"            => "\xe1\xb9\xa2",
      "s\xcc\xa3"            => "\xe1\xb9\xa3",
      "\xc5\x9a\xcc\x87"     => "\xe1\xb9\xa4",
      "\xc5\x9b\xcc\x87"     => "\xe1\xb9\xa5",
      "\xc5\xa0\xcc\x87"     => "\xe1\xb9\xa6",
      "\xc5\xa1\xcc\x87"     => "\xe1\xb9\xa7",
      "\xe1\xb9\xa2\xcc\x87" => "\xe1\xb9\xa8",
      "\xe1\xb9\xa3\xcc\x87" => "\xe1\xb9\xa9",
      "T\xcc\x87"            => "\xe1\xb9\xaa",
      "t\xcc\x87"            => "\xe1\xb9\xab",
      "T\xcc\xa3"            => "\xe1\xb9\xac",
      "t\xcc\xa3"            => "\xe1\xb9\xad",
      "T\xcc\xb1"            => "\xe1\xb9\xae",
      "t\xcc\xb1"            => "\xe1\xb9\xaf",
      "T\xcc\xad"            => "\xe1\xb9\xb0",
      "t\xcc\xad"            => "\xe1\xb9\xb1",
      "U\xcc\xa4"            => "\xe1\xb9\xb2",
      "u\xcc\xa4"            => "\xe1\xb9\xb3",
      "U\xcc\xb0"            => "\xe1\xb9\xb4",
      "u\xcc\xb0"            => "\xe1\xb9\xb5",
      "U\xcc\xad"            => "\xe1\xb9\xb6",
      "u\xcc\xad"            => "\xe1\xb9\xb7",
      "\xc5\xa8\xcc\x81"     => "\xe1\xb9\xb8",
      "\xc5\xa9\xcc\x81"     => "\xe1\xb9\xb9",
      "\xc5\xaa\xcc\x88"     => "\xe1\xb9\xba",
      "\xc5\xab\xcc\x88"     => "\xe1\xb9\xbb",
      "V\xcc\x83"            => "\xe1\xb9\xbc",
      "v\xcc\x83"            => "\xe1\xb9\xbd",
      "V\xcc\xa3"            => "\xe1\xb9\xbe",
      "v\xcc\xa3"            => "\xe1\xb9\xbf",
      "W\xcc\x80"            => "\xe1\xba\x80",
      "w\xcc\x80"            => "\xe1\xba\x81",
      "W\xcc\x81"            => "\xe1\xba\x82",
      "w\xcc\x81"            => "\xe1\xba\x83",
      "W\xcc\x88"            => "\xe1\xba\x84",
      "w\xcc\x88"            => "\xe1\xba\x85",
      "W\xcc\x87"            => "\xe1\xba\x86",
      "w\xcc\x87"            => "\xe1\xba\x87",
      "W\xcc\xa3"            => "\xe1\xba\x88",
      "w\xcc\xa3"            => "\xe1\xba\x89",
      "X\xcc\x87"            => "\xe1\xba\x8a",
      "x\xcc\x87"            => "\xe1\xba\x8b",
      "X\xcc\x88"            => "\xe1\xba\x8c",
      "x\xcc\x88"            => "\xe1\xba\x8d",
      "Y\xcc\x87"            => "\xe1\xba\x8e",
      "y\xcc\x87"            => "\xe1\xba\x8f",
      "Z\xcc\x82"            => "\xe1\xba\x90",
      "z\xcc\x82"            => "\xe1\xba\x91",
      "Z\xcc\xa3"            => "\xe1\xba\x92",
      "z\xcc\xa3"            => "\xe1\xba\x93",
      "Z\xcc\xb1"            => "\xe1\xba\x94",
      "z\xcc\xb1"            => "\xe1\xba\x95",
      "h\xcc\xb1"            => "\xe1\xba\x96",
      "t\xcc\x88"            => "\xe1\xba\x97",
      "w\xcc\x8a"            => "\xe1\xba\x98",
      "y\xcc\x8a"            => "\xe1\xba\x99",
      "\xc5\xbf\xcc\x87"     => "\xe1\xba\x9b",
      "A\xcc\xa3"            => "\xe1\xba\xa0",
      "a\xcc\xa3"            => "\xe1\xba\xa1",
      "A\xcc\x89"            => "\xe1\xba\xa2",
      "a\xcc\x89"            => "\xe1\xba\xa3",
      "\xc3\x82\xcc\x81"     => "\xe1\xba\xa4",
      "\xc3\xa2\xcc\x81"     => "\xe1\xba\xa5",
      "\xc3\x82\xcc\x80"     => "\xe1\xba\xa6",
      "\xc3\xa2\xcc\x80"     => "\xe1\xba\xa7",
      "\xc3\x82\xcc\x89"     => "\xe1\xba\xa8",
      "\xc3\xa2\xcc\x89"     => "\xe1\xba\xa9",
      "\xc3\x82\xcc\x83"     => "\xe1\xba\xaa",
      "\xc3\xa2\xcc\x83"     => "\xe1\xba\xab",
      "\xe1\xba\xa0\xcc\x82" => "\xe1\xba\xac",
      "\xe1\xba\xa1\xcc\x82" => "\xe1\xba\xad",
      "\xc4\x82\xcc\x81"     => "\xe1\xba\xae",
      "\xc4\x83\xcc\x81"     => "\xe1\xba\xaf",
      "\xc4\x82\xcc\x80"     => "\xe1\xba\xb0",
      "\xc4\x83\xcc\x80"     => "\xe1\xba\xb1",
      "\xc4\x82\xcc\x89"     => "\xe1\xba\xb2",
      "\xc4\x83\xcc\x89"     => "\xe1\xba\xb3",
      "\xc4\x82\xcc\x83"     => "\xe1\xba\xb4",
      "\xc4\x83\xcc\x83"     => "\xe1\xba\xb5",
      "\xe1\xba\xa0\xcc\x86" => "\xe1\xba\xb6",
      "\xe1\xba\xa1\xcc\x86" => "\xe1\xba\xb7",
      "E\xcc\xa3"            => "\xe1\xba\xb8",
      "e\xcc\xa3"            => "\xe1\xba\xb9",
      "E\xcc\x89"            => "\xe1\xba\xba",
      "e\xcc\x89"            => "\xe1\xba\xbb",
      "E\xcc\x83"            => "\xe1\xba\xbc",
      "e\xcc\x83"            => "\xe1\xba\xbd",
      "\xc3\x8a\xcc\x81"     => "\xe1\xba\xbe",
      "\xc3\xaa\xcc\x81"     => "\xe1\xba\xbf",
      "\xc3\x8a\xcc\x80"     => "\xe1\xbb\x80",
      "\xc3\xaa\xcc\x80"     => "\xe1\xbb\x81",
      "\xc3\x8a\xcc\x89"     => "\xe1\xbb\x82",
      "\xc3\xaa\xcc\x89"     => "\xe1\xbb\x83",
      "\xc3\x8a\xcc\x83"     => "\xe1\xbb\x84",
      "\xc3\xaa\xcc\x83"     => "\xe1\xbb\x85",
      "\xe1\xba\xb8\xcc\x82" => "\xe1\xbb\x86",
      "\xe1\xba\xb9\xcc\x82" => "\xe1\xbb\x87",
      "I\xcc\x89"            => "\xe1\xbb\x88",
      "i\xcc\x89"            => "\xe1\xbb\x89",
      "I\xcc\xa3"            => "\xe1\xbb\x8a",
      "i\xcc\xa3"            => "\xe1\xbb\x8b",
      "O\xcc\xa3"            => "\xe1\xbb\x8c",
      "o\xcc\xa3"            => "\xe1\xbb\x8d",
      "O\xcc\x89"            => "\xe1\xbb\x8e",
      "o\xcc\x89"            => "\xe1\xbb\x8f",
      "\xc3\x94\xcc\x81"     => "\xe1\xbb\x90",
      "\xc3\xb4\xcc\x81"     => "\xe1\xbb\x91",
      "\xc3\x94\xcc\x80"     => "\xe1\xbb\x92",
      "\xc3\xb4\xcc\x80"     => "\xe1\xbb\x93",
      "\xc3\x94\xcc\x89"     => "\xe1\xbb\x94",
      "\xc3\xb4\xcc\x89"     => "\xe1\xbb\x95",
      "\xc3\x94\xcc\x83"     => "\xe1\xbb\x96",
      "\xc3\xb4\xcc\x83"     => "\xe1\xbb\x97",
      "\xe1\xbb\x8c\xcc\x82" => "\xe1\xbb\x98",
      "\xe1\xbb\x8d\xcc\x82" => "\xe1\xbb\x99",
      "\xc6\xa0\xcc\x81"     => "\xe1\xbb\x9a",
      "\xc6\xa1\xcc\x81"     => "\xe1\xbb\x9b",
      "\xc6\xa0\xcc\x80"     => "\xe1\xbb\x9c",
      "\xc6\xa1\xcc\x80"     => "\xe1\xbb\x9d",
      "\xc6\xa0\xcc\x89"     => "\xe1\xbb\x9e",
      "\xc6\xa1\xcc\x89"     => "\xe1\xbb\x9f",
      "\xc6\xa0\xcc\x83"     => "\xe1\xbb\xa0",
      "\xc6\xa1\xcc\x83"     => "\xe1\xbb\xa1",
      "\xc6\xa0\xcc\xa3"     => "\xe1\xbb\xa2",
      "\xc6\xa1\xcc\xa3"     => "\xe1\xbb\xa3",
      "U\xcc\xa3"            => "\xe1\xbb\xa4",
      "u\xcc\xa3"            => "\xe1\xbb\xa5",
      "U\xcc\x89"            => "\xe1\xbb\xa6",
      "u\xcc\x89"            => "\xe1\xbb\xa7",
      "\xc6\xaf\xcc\x81"     => "\xe1\xbb\xa8",
      "\xc6\xb0\xcc\x81"     => "\xe1\xbb\xa9",
      "\xc6\xaf\xcc\x80"     => "\xe1\xbb\xaa",
      "\xc6\xb0\xcc\x80"     => "\xe1\xbb\xab",
      "\xc6\xaf\xcc\x89"     => "\xe1\xbb\xac",
      "\xc6\xb0\xcc\x89"     => "\xe1\xbb\xad",
      "\xc6\xaf\xcc\x83"     => "\xe1\xbb\xae",
      "\xc6\xb0\xcc\x83"     => "\xe1\xbb\xaf",
      "\xc6\xaf\xcc\xa3"     => "\xe1\xbb\xb0",
      "\xc6\xb0\xcc\xa3"     => "\xe1\xbb\xb1",
      "Y\xcc\x80"            => "\xe1\xbb\xb2",
      "y\xcc\x80"            => "\xe1\xbb\xb3",
      "Y\xcc\xa3"            => "\xe1\xbb\xb4",
      "y\xcc\xa3"            => "\xe1\xbb\xb5",
      "Y\xcc\x89"            => "\xe1\xbb\xb6",
      "y\xcc\x89"            => "\xe1\xbb\xb7",
      "Y\xcc\x83"            => "\xe1\xbb\xb8",
      "y\xcc\x83"            => "\xe1\xbb\xb9",
      "\xce\xb1\xcc\x93"     => "\xe1\xbc\x80",
      "\xce\xb1\xcc\x94"     => "\xe1\xbc\x81",
      "\xe1\xbc\x80\xcc\x80" => "\xe1\xbc\x82",
      "\xe1\xbc\x81\xcc\x80" => "\xe1\xbc\x83",
      "\xe1\xbc\x80\xcc\x81" => "\xe1\xbc\x84",
      "\xe1\xbc\x81\xcc\x81" => "\xe1\xbc\x85",
      "\xe1\xbc\x80\xcd\x82" => "\xe1\xbc\x86",
      "\xe1\xbc\x81\xcd\x82" => "\xe1\xbc\x87",
      "\xce\x91\xcc\x93"     => "\xe1\xbc\x88",
      "\xce\x91\xcc\x94"     => "\xe1\xbc\x89",
      "\xe1\xbc\x88\xcc\x80" => "\xe1\xbc\x8a",
      "\xe1\xbc\x89\xcc\x80" => "\xe1\xbc\x8b",
      "\xe1\xbc\x88\xcc\x81" => "\xe1\xbc\x8c",
      "\xe1\xbc\x89\xcc\x81" => "\xe1\xbc\x8d",
      "\xe1\xbc\x88\xcd\x82" => "\xe1\xbc\x8e",
      "\xe1\xbc\x89\xcd\x82" => "\xe1\xbc\x8f",
      "\xce\xb5\xcc\x93"     => "\xe1\xbc\x90",
      "\xce\xb5\xcc\x94"     => "\xe1\xbc\x91",
      "\xe1\xbc\x90\xcc\x80" => "\xe1\xbc\x92",
      "\xe1\xbc\x91\xcc\x80" => "\xe1\xbc\x93",
      "\xe1\xbc\x90\xcc\x81" => "\xe1\xbc\x94",
      "\xe1\xbc\x91\xcc\x81" => "\xe1\xbc\x95",
      "\xce\x95\xcc\x93"     => "\xe1\xbc\x98",
      "\xce\x95\xcc\x94"     => "\xe1\xbc\x99",
      "\xe1\xbc\x98\xcc\x80" => "\xe1\xbc\x9a",
      "\xe1\xbc\x99\xcc\x80" => "\xe1\xbc\x9b",
      "\xe1\xbc\x98\xcc\x81" => "\xe1\xbc\x9c",
      "\xe1\xbc\x99\xcc\x81" => "\xe1\xbc\x9d",
      "\xce\xb7\xcc\x93"     => "\xe1\xbc\xa0",
      "\xce\xb7\xcc\x94"     => "\xe1\xbc\xa1",
      "\xe1\xbc\xa0\xcc\x80" => "\xe1\xbc\xa2",
      "\xe1\xbc\xa1\xcc\x80" => "\xe1\xbc\xa3",
      "\xe1\xbc\xa0\xcc\x81" => "\xe1\xbc\xa4",
      "\xe1\xbc\xa1\xcc\x81" => "\xe1\xbc\xa5",
      "\xe1\xbc\xa0\xcd\x82" => "\xe1\xbc\xa6",
      "\xe1\xbc\xa1\xcd\x82" => "\xe1\xbc\xa7",
      "\xce\x97\xcc\x93"     => "\xe1\xbc\xa8",
      "\xce\x97\xcc\x94"     => "\xe1\xbc\xa9",
      "\xe1\xbc\xa8\xcc\x80" => "\xe1\xbc\xaa",
      "\xe1\xbc\xa9\xcc\x80" => "\xe1\xbc\xab",
      "\xe1\xbc\xa8\xcc\x81" => "\xe1\xbc\xac",
      "\xe1\xbc\xa9\xcc\x81" => "\xe1\xbc\xad",
      "\xe1\xbc\xa8\xcd\x82" => "\xe1\xbc\xae",
      "\xe1\xbc\xa9\xcd\x82" => "\xe1\xbc\xaf",
      "\xce\xb9\xcc\x93"     => "\xe1\xbc\xb0",
      "\xce\xb9\xcc\x94"     => "\xe1\xbc\xb1",
      "\xe1\xbc\xb0\xcc\x80" => "\xe1\xbc\xb2",
      "\xe1\xbc\xb1\xcc\x80" => "\xe1\xbc\xb3",
      "\xe1\xbc\xb0\xcc\x81" => "\xe1\xbc\xb4",
      "\xe1\xbc\xb1\xcc\x81" => "\xe1\xbc\xb5",
      "\xe1\xbc\xb0\xcd\x82" => "\xe1\xbc\xb6",
      "\xe1\xbc\xb1\xcd\x82" => "\xe1\xbc\xb7",
      "\xce\x99\xcc\x93"     => "\xe1\xbc\xb8",
      "\xce\x99\xcc\x94"     => "\xe1\xbc\xb9",
      "\xe1\xbc\xb8\xcc\x80" => "\xe1\xbc\xba",
      "\xe1\xbc\xb9\xcc\x80" => "\xe1\xbc\xbb",
      "\xe1\xbc\xb8\xcc\x81" => "\xe1\xbc\xbc",
      "\xe1\xbc\xb9\xcc\x81" => "\xe1\xbc\xbd",
      "\xe1\xbc\xb8\xcd\x82" => "\xe1\xbc\xbe",
      "\xe1\xbc\xb9\xcd\x82" => "\xe1\xbc\xbf",
      "\xce\xbf\xcc\x93"     => "\xe1\xbd\x80",
      "\xce\xbf\xcc\x94"     => "\xe1\xbd\x81",
      "\xe1\xbd\x80\xcc\x80" => "\xe1\xbd\x82",
      "\xe1\xbd\x81\xcc\x80" => "\xe1\xbd\x83",
      "\xe1\xbd\x80\xcc\x81" => "\xe1\xbd\x84",
      "\xe1\xbd\x81\xcc\x81" => "\xe1\xbd\x85",
      "\xce\x9f\xcc\x93"     => "\xe1\xbd\x88",
      "\xce\x9f\xcc\x94"     => "\xe1\xbd\x89",
      "\xe1\xbd\x88\xcc\x80" => "\xe1\xbd\x8a",
      "\xe1\xbd\x89\xcc\x80" => "\xe1\xbd\x8b",
      "\xe1\xbd\x88\xcc\x81" => "\xe1\xbd\x8c",
      "\xe1\xbd\x89\xcc\x81" => "\xe1\xbd\x8d",
      "\xcf\x85\xcc\x93"     => "\xe1\xbd\x90",
      "\xcf\x85\xcc\x94"     => "\xe1\xbd\x91",
      "\xe1\xbd\x90\xcc\x80" => "\xe1\xbd\x92",
      "\xe1\xbd\x91\xcc\x80" => "\xe1\xbd\x93",
      "\xe1\xbd\x90\xcc\x81" => "\xe1\xbd\x94",
      "\xe1\xbd\x91\xcc\x81" => "\xe1\xbd\x95",
      "\xe1\xbd\x90\xcd\x82" => "\xe1\xbd\x96",
      "\xe1\xbd\x91\xcd\x82" => "\xe1\xbd\x97",
      "\xce\xa5\xcc\x94"     => "\xe1\xbd\x99",
      "\xe1\xbd\x99\xcc\x80" => "\xe1\xbd\x9b",
      "\xe1\xbd\x99\xcc\x81" => "\xe1\xbd\x9d",
      "\xe1\xbd\x99\xcd\x82" => "\xe1\xbd\x9f",
      "\xcf\x89\xcc\x93"     => "\xe1\xbd\xa0",
      "\xcf\x89\xcc\x94"     => "\xe1\xbd\xa1",
      "\xe1\xbd\xa0\xcc\x80" => "\xe1\xbd\xa2",
      "\xe1\xbd\xa1\xcc\x80" => "\xe1\xbd\xa3",
      "\xe1\xbd\xa0\xcc\x81" => "\xe1\xbd\xa4",
      "\xe1\xbd\xa1\xcc\x81" => "\xe1\xbd\xa5",
      "\xe1\xbd\xa0\xcd\x82" => "\xe1\xbd\xa6",
      "\xe1\xbd\xa1\xcd\x82" => "\xe1\xbd\xa7",
      "\xce\xa9\xcc\x93"     => "\xe1\xbd\xa8",
      "\xce\xa9\xcc\x94"     => "\xe1\xbd\xa9",
      "\xe1\xbd\xa8\xcc\x80" => "\xe1\xbd\xaa",
      "\xe1\xbd\xa9\xcc\x80" => "\xe1\xbd\xab",
      "\xe1\xbd\xa8\xcc\x81" => "\xe1\xbd\xac",
      "\xe1\xbd\xa9\xcc\x81" => "\xe1\xbd\xad",
      "\xe1\xbd\xa8\xcd\x82" => "\xe1\xbd\xae",
      "\xe1\xbd\xa9\xcd\x82" => "\xe1\xbd\xaf",
      "\xce\xb1\xcc\x80"     => "\xe1\xbd\xb0",
      "\xce\xb5\xcc\x80"     => "\xe1\xbd\xb2",
      "\xce\xb7\xcc\x80"     => "\xe1\xbd\xb4",
      "\xce\xb9\xcc\x80"     => "\xe1\xbd\xb6",
      "\xce\xbf\xcc\x80"     => "\xe1\xbd\xb8",
      "\xcf\x85\xcc\x80"     => "\xe1\xbd\xba",
      "\xcf\x89\xcc\x80"     => "\xe1\xbd\xbc",
      "\xe1\xbc\x80\xcd\x85" => "\xe1\xbe\x80",
      "\xe1\xbc\x81\xcd\x85" => "\xe1\xbe\x81",
      "\xe1\xbc\x82\xcd\x85" => "\xe1\xbe\x82",
      "\xe1\xbc\x83\xcd\x85" => "\xe1\xbe\x83",
      "\xe1\xbc\x84\xcd\x85" => "\xe1\xbe\x84",
      "\xe1\xbc\x85\xcd\x85" => "\xe1\xbe\x85",
      "\xe1\xbc\x86\xcd\x85" => "\xe1\xbe\x86",
      "\xe1\xbc\x87\xcd\x85" => "\xe1\xbe\x87",
      "\xe1\xbc\x88\xcd\x85" => "\xe1\xbe\x88",
      "\xe1\xbc\x89\xcd\x85" => "\xe1\xbe\x89",
      "\xe1\xbc\x8a\xcd\x85" => "\xe1\xbe\x8a",
      "\xe1\xbc\x8b\xcd\x85" => "\xe1\xbe\x8b",
      "\xe1\xbc\x8c\xcd\x85" => "\xe1\xbe\x8c",
      "\xe1\xbc\x8d\xcd\x85" => "\xe1\xbe\x8d",
      "\xe1\xbc\x8e\xcd\x85" => "\xe1\xbe\x8e",
      "\xe1\xbc\x8f\xcd\x85" => "\xe1\xbe\x8f",
      "\xe1\xbc\xa0\xcd\x85" => "\xe1\xbe\x90",
      "\xe1\xbc\xa1\xcd\x85" => "\xe1\xbe\x91",
      "\xe1\xbc\xa2\xcd\x85" => "\xe1\xbe\x92",
      "\xe1\xbc\xa3\xcd\x85" => "\xe1\xbe\x93",
      "\xe1\xbc\xa4\xcd\x85" => "\xe1\xbe\x94",
      "\xe1\xbc\xa5\xcd\x85" => "\xe1\xbe\x95",
      "\xe1\xbc\xa6\xcd\x85" => "\xe1\xbe\x96",
      "\xe1\xbc\xa7\xcd\x85" => "\xe1\xbe\x97",
      "\xe1\xbc\xa8\xcd\x85" => "\xe1\xbe\x98",
      "\xe1\xbc\xa9\xcd\x85" => "\xe1\xbe\x99",
      "\xe1\xbc\xaa\xcd\x85" => "\xe1\xbe\x9a",
      "\xe1\xbc\xab\xcd\x85" => "\xe1\xbe\x9b",
      "\xe1\xbc\xac\xcd\x85" => "\xe1\xbe\x9c",
      "\xe1\xbc\xad\xcd\x85" => "\xe1\xbe\x9d",
      "\xe1\xbc\xae\xcd\x85" => "\xe1\xbe\x9e",
      "\xe1\xbc\xaf\xcd\x85" => "\xe1\xbe\x9f",
      "\xe1\xbd\xa0\xcd\x85" => "\xe1\xbe\xa0",
      "\xe1\xbd\xa1\xcd\x85" => "\xe1\xbe\xa1",
      "\xe1\xbd\xa2\xcd\x85" => "\xe1\xbe\xa2",
      "\xe1\xbd\xa3\xcd\x85" => "\xe1\xbe\xa3",
      "\xe1\xbd\xa4\xcd\x85" => "\xe1\xbe\xa4",
      "\xe1\xbd\xa5\xcd\x85" => "\xe1\xbe\xa5",
      "\xe1\xbd\xa6\xcd\x85" => "\xe1\xbe\xa6",
      "\xe1\xbd\xa7\xcd\x85" => "\xe1\xbe\xa7",
      "\xe1\xbd\xa8\xcd\x85" => "\xe1\xbe\xa8",
      "\xe1\xbd\xa9\xcd\x85" => "\xe1\xbe\xa9",
      "\xe1\xbd\xaa\xcd\x85" => "\xe1\xbe\xaa",
      "\xe1\xbd\xab\xcd\x85" => "\xe1\xbe\xab",
      "\xe1\xbd\xac\xcd\x85" => "\xe1\xbe\xac",
      "\xe1\xbd\xad\xcd\x85" => "\xe1\xbe\xad",
      "\xe1\xbd\xae\xcd\x85" => "\xe1\xbe\xae",
      "\xe1\xbd\xaf\xcd\x85" => "\xe1\xbe\xaf",
      "\xce\xb1\xcc\x86"     => "\xe1\xbe\xb0",
      "\xce\xb1\xcc\x84"     => "\xe1\xbe\xb1",
      "\xe1\xbd\xb0\xcd\x85" => "\xe1\xbe\xb2",
      "\xce\xb1\xcd\x85"     => "\xe1\xbe\xb3",
      "\xce\xac\xcd\x85"     => "\xe1\xbe\xb4",
      "\xce\xb1\xcd\x82"     => "\xe1\xbe\xb6",
      "\xe1\xbe\xb6\xcd\x85" => "\xe1\xbe\xb7",
      "\xce\x91\xcc\x86"     => "\xe1\xbe\xb8",
      "\xce\x91\xcc\x84"     => "\xe1\xbe\xb9",
      "\xce\x91\xcc\x80"     => "\xe1\xbe\xba",
      "\xce\x91\xcd\x85"     => "\xe1\xbe\xbc",
      "\xc2\xa8\xcd\x82"     => "\xe1\xbf\x81",
      "\xe1\xbd\xb4\xcd\x85" => "\xe1\xbf\x82",
      "\xce\xb7\xcd\x85"     => "\xe1\xbf\x83",
      "\xce\xae\xcd\x85"     => "\xe1\xbf\x84",
      "\xce\xb7\xcd\x82"     => "\xe1\xbf\x86",
      "\xe1\xbf\x86\xcd\x85" => "\xe1\xbf\x87",
      "\xce\x95\xcc\x80"     => "\xe1\xbf\x88",
      "\xce\x97\xcc\x80"     => "\xe1\xbf\x8a",
      "\xce\x97\xcd\x85"     => "\xe1\xbf\x8c",
      "\xe1\xbe\xbf\xcc\x80" => "\xe1\xbf\x8d",
      "\xe1\xbe\xbf\xcc\x81" => "\xe1\xbf\x8e",
      "\xe1\xbe\xbf\xcd\x82" => "\xe1\xbf\x8f",
      "\xce\xb9\xcc\x86"     => "\xe1\xbf\x90",
      "\xce\xb9\xcc\x84"     => "\xe1\xbf\x91",
      "\xcf\x8a\xcc\x80"     => "\xe1\xbf\x92",
      "\xce\xb9\xcd\x82"     => "\xe1\xbf\x96",
      "\xcf\x8a\xcd\x82"     => "\xe1\xbf\x97",
      "\xce\x99\xcc\x86"     => "\xe1\xbf\x98",
      "\xce\x99\xcc\x84"     => "\xe1\xbf\x99",
      "\xce\x99\xcc\x80"     => "\xe1\xbf\x9a",
      "\xe1\xbf\xbe\xcc\x80" => "\xe1\xbf\x9d",
      "\xe1\xbf\xbe\xcc\x81" => "\xe1\xbf\x9e",
      "\xe1\xbf\xbe\xcd\x82" => "\xe1\xbf\x9f",
      "\xcf\x85\xcc\x86"     => "\xe1\xbf\xa0",
      "\xcf\x85\xcc\x84"     => "\xe1\xbf\xa1",
      "\xcf\x8b\xcc\x80"     => "\xe1\xbf\xa2",
      "\xcf\x81\xcc\x93"     => "\xe1\xbf\xa4",
      "\xcf\x81\xcc\x94"     => "\xe1\xbf\xa5",
      "\xcf\x85\xcd\x82"     => "\xe1\xbf\xa6",
      "\xcf\x8b\xcd\x82"     => "\xe1\xbf\xa7",
      "\xce\xa5\xcc\x86"     => "\xe1\xbf\xa8",
      "\xce\xa5\xcc\x84"     => "\xe1\xbf\xa9",
      "\xce\xa5\xcc\x80"     => "\xe1\xbf\xaa",
      "\xce\xa1\xcc\x94"     => "\xe1\xbf\xac",
      "\xc2\xa8\xcc\x80"     => "\xe1\xbf\xad",
      "\xe1\xbd\xbc\xcd\x85" => "\xe1\xbf\xb2",
      "\xcf\x89\xcd\x85"     => "\xe1\xbf\xb3",
      "\xcf\x8e\xcd\x85"     => "\xe1\xbf\xb4",
      "\xcf\x89\xcd\x82"     => "\xe1\xbf\xb6",
      "\xe1\xbf\xb6\xcd\x85" => "\xe1\xbf\xb7",
      "\xce\x9f\xcc\x80"     => "\xe1\xbf\xb8",
      "\xce\xa9\xcc\x80"     => "\xe1\xbf\xba",
      "\xce\xa9\xcd\x85"     => "\xe1\xbf\xbc"
    );

    return strtr($string, $conv);
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
  static function truncate($string, $max = 25, $replacement = '...') {
    if (is_object($string)) {
      return $string;
    }

    if (strlen($string) > $max) {
      return substr($string, 0, $max - strlen($replacement)) . $replacement;
    }

    return $string;
  }

  /**
   * Puts the string to uppercase
   *
   * @param String $string Chain to be uppercase
   *
   * @return string
   */
  static function upper($string) {
    return mb_strtoupper($string, CApp::$encoding);
  }

  /**
   * Puts the string to lowercase
   *
   * @param String $string Chain to be lowercase
   *
   * @return string
   */
  static function lower($string) {
    return mb_strtolower($string, CApp::$encoding);
  }

  /**
   * Capitalize the chain
   *
   * @param String $string Chain to be capitalize
   *
   * @return string
   */
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
    return self::fromBytes($value, false) . "i$unit";
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
    return self::fromBytes($value, true) . $unit;
  }

  /**
   * Transforms a number of bytes in string
   *
   * @param Integer $value Number of bytes
   * @param boolean $si    Use the real valor
   *
   * @return string
   */
  private static function fromBytes($value, $si = false) {
    $bytes  = $value;
    $suffix = "";
    $ratio  = ($si ? 1000 : 1024);

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value  = $bytes;
      $suffix = ($si ? "k" : "K");
    }

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value  = $bytes;
      $suffix = "M";
    }

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value  = $bytes;
      $suffix = "G";
    }

    $bytes = $bytes / $ratio;
    if ($bytes >= 1) {
      $value  = $bytes;
      $suffix = "T";
    }

    // Value with 3 significant digits
    $value = number_format($value, 2 - intval(log10($value)));

    return "$value$suffix";
  }

  /**
   * Transforms a string into a byte number
   *
   * @param String $string Chain to convert
   * @param bool   $si     Use the real valor
   *
   * @return int
   */
  private static function toBytes($string, $si = false) {
    $ratio  = ($si ? 1000 : 1024);
    $string = strtolower(trim($string));

    if (!preg_match("/^([,\.\d]+)([kmgt])/", $string, $matches)) {
      return intval($string);
    }

    list($string, $value, $suffix) = $matches;

    switch ($suffix) {
      case 't':
        $value *= $ratio;
      case 'g':
        $value *= $ratio;
      case 'm':
        $value *= $ratio;
      case 'k':
        $value *= $ratio;
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

  /**
   * Unslash a string
   *
   * @param String $str String to unslash
   *
   * @return string
   */
  static function unslash($str) {
    $character = array(
      "\\n" => "\n",
      "\\t" => "\t",
    );

    return strtr($str, $character);
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
   * @param string $string Input string
   * @param int    $flags  Flags
   *
   * @return string
   */
  static function htmlSpecialChars($string, $flags = ENT_COMPAT) {
    return htmlspecialchars($string, $flags, CApp::$encoding);
  }

  /**
   * Equivalent to htmlentities
   *
   * @param string $string Input string
   * @param int    $flags  Flags
   *
   * @return string
   */
  static function htmlEntities($string, $flags = ENT_COMPAT) {
    return htmlentities($string, $flags, CApp::$encoding);
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

  /**
   * Verifies that the string is UTF-8
   *
   * @param String $string Chain to test
   *
   * @return bool
   */
  static function isUTF8($string) {
    return mb_detect_encoding($string) === "UTF-8";
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
    return preg_replace('@(https?://([^<>][-\w\.]+)+(:\d+)?(/([\w/_\.#-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $str);
  }

  /**
   * Convert HTML into XMLEntities
   *
   * Table extraite de :
   * - http://www.sourcerally.net/Scripts/39-Convert-HTML-Entities-to-XML-Entities
   * - http://yost.com/computers/htmlchars/html40charsbynumber.html
   *
   * @param String $str Chain to convert
   *
   * @return mixed
   */
  static function convertHTMLToXMLEntities($str) {
    $xml = array('&#34;', '&#38;', '&#60;', '&#62;', '&#160;', '&#161;', '&#162;',
      '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;',
      '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;',
      '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;',
      '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;',
      '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;',
      '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;',
      '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;',
      '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;',
      '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;',
      '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;',
      '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;',
      '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;',
      '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;',
      '&#254;', '&#255;', '&#338;', '&#339;', '&#352;', '&#353;', '&#376;',
      '&#402;',
      '&#710;', '&#732;',
      '&#913;', '&#914;', '&#915;', '&#916;', '&#917;', '&#918;', '&#919;',
      '&#920;', '&#921;', '&#922;', '&#923;', '&#924;', '&#925;', '&#926;',
      '&#927;', '&#928;', '&#929;', '&#931;', '&#932;', '&#933;', '&#934;',
      '&#935;', '&#936;', '&#937;', '&#945;', '&#946;', '&#947;', '&#948;',
      '&#949;', '&#950;', '&#951;', '&#952;', '&#953;', '&#954;', '&#955;',
      '&#956;', '&#957;', '&#958;', '&#959;', '&#960;', '&#961;', '&#962;',
      '&#963;', '&#964;', '&#965;', '&#966;', '&#967;', '&#968;', '&#969;',
      '&#977;', '&#978;', '&#982;',
      '&#8194;', '&#8195;', '&#8201;', '&#8204;', '&#8205;', '&#8206;', '&#8207;',
      '&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8218;',
      '&#8220;', '&#8221;', '&#8222;', '&#8224;', '&#8225;', '&#8226;', '&#8230;',
      '&#8240;', '&#8242;', '&#8243;', '&#8249;', '&#8250;', '&#8254;', '&#8260;',
      '&#8364;',
      '&#8465;', '&#8472;', '&#8476;', '&#8482;',
      '&#8501;', '&#8592;', '&#8593;', '&#8594;', '&#8595;', '&#8596;',
      '&#8629;', '&#8656;', '&#8657;', '&#8658;', '&#8659;', '&#8660;',
      '&#8704;', '&#8706;', '&#8707;', '&#8709;', '&#8711;', '&#8712;', '&#8713;',
      '&#8715;', '&#8719;', '&#8721;', '&#8722;', '&#8727;', '&#8730;', '&#8733;',
      '&#8734;', '&#8736;', '&#8743;', '&#8744;', '&#8745;', '&#8746;', '&#8747;',
      '&#8756;', '&#8764;', '&#8773;', '&#8776;',
      '&#8800;', '&#8801;', '&#8804;', '&#8805;', '&#8834;', '&#8835;', '&#8836;',
      '&#8838;', '&#8839;', '&#8853;', '&#8855;', '&#8869;',
      '&#8901;', '&#8968;', '&#8969;', '&#8970;', '&#8971;',
      '&#9001', '&#9002;',
      '&#9674;', '&#9824;', '&#9827;', '&#9829;', '&#9830;');

    $html = array('&quot;', '&amp;', '&lt;', '&gt;', '&nbsp;', '&iexcl;', '&cent;',
      '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;',
      '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;',
      '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;',
      '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;',
      '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;',
      '&AElig;', ' &Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;',
      '&Iacute;', ' &Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;',
      '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;',
      '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;',
      '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;',
      '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;',
      '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;',
      '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;',
      '&thorn;', '&yuml;', '&OElig;', '&oelig;', '&Scaron;', '&scaron;', '&Yuml;',
      '&fnof;',
      '&circ;', '&tilde;',
      '&Alpha;', '&Beta;', '&Gamma;', '&Delta;', '&Epsilon;', '&Zeta;', '&Eta;',
      '&Theta;', '&Iota;', '&Kappa;', '&Lambda;', '&Mu;', '&Nu;', '&Xi;',
      '&Omicron;', '&Pi;', '&Rho;', '&Sigma;', '&Tau;', '&Upsilon;', '&Phi;',
      '&Chi;', '&Psi;', '&Omega;', '&alpha;', '&beta;', '&gamma;', '&delta;',
      '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;',
      '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;',
      '&sigma;', '&tau;', '&upsilon;', '&phi;', '&#chi;', '&psi;', '&omega;',
      '&thetasym;', '&upsih;', '&piv;',
      '&ensp;', '&emsp;', '&thinsp;', '&zwnj;', '&zwj;', '&lrm;', '&rlm;',
      '&ndash;',
      '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;',
      '&ldquo;', '&rdquo;', '&bdquo;', '&dagger;', '&Dagger;', '&bull;', '&hellip;',
      '&permil;', '&prime;', '&Prime;', '&lsaquo;', '&rsaquo;', '&oline;', '&frasl;',
      '&euro;',
      '&image;', '&weierp;', '&real;', '&trade;',
      '&alefsym;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;',
      '&crarr;', '&lArr;', '&uArr;', '&rArr;', '&dArr;', '&hArr;',
      '&forall;', '&part;', '&exist;', '&empty;', '&nabla;', '&isin;', '&notin;',
      '&ni;', '&prod;', '&sum;', '&minus;', '&lowast;', '&radic;', '&prop;',
      '&infin;', '&ang;', '&and;', '&or;', '&cap;', '&cup;', '&int;',
      '&there4;', '&sim;', '&cong;', '&asymp;',
      '&ne;', '&equiv;', '&le;', '&ge;', '&sub;', '&sup;', '&nsub;',
      '&sube;', '&supe;', '&oplus;', '&otimes;', '&perp;',
      '&sdot;', '&lceil;', '&rceil;', '&lfloor;', '&rfloor;',
      '&lang;', '&rang;',
      '&loz;', '&spades;', '&clubs;', '&hearts;', '&diams;');

    $str = str_replace($html, $xml, $str);
    $str = str_ireplace($html, $xml, $str);

    return $str;
  }

  /**
   * Apply syntax highlighting
   *
   * @param String $language       Code language
   * @param String $code           Code to format
   * @param bool   $enable_classes Enable the CSS class
   * @param string $style          CSS class to apply
   *
   * @return mixed
   */
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

  /**
   * Transforms the number to a string
   *
   * @param Integer $num Number to transforms
   *
   * @return string
   */
  static function toWords($num) {
    @list($whole, $decimal) = @preg_split('/[.,]/', $num);

    $nw    = new nuts($whole, "");
    $words = $nw->convert("fr-FR");

    if ($decimal) {
      $nw = new nuts($decimal, "");
      $words .= " virgule " . $nw->convert("fr-FR");
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

  /**
   * Filter empty strings
   *
   * @param array $strings An array of strings
   *
   * @return array Filtered array, without empty strings
   */
  static function filterEmpty(array $strings) {
    return array_filter(
      $strings,
      function ($string) {
        return $string !== "";
      }
    );
  }

  /**
   * HTML cleaning method
   *
   * @param string $html HTML to purify
   *
   * @return string
   */
  static function purifyHTML($html) {
    if (trim($html) == "") {
      return $html;
    }

    static $cache = array();
    static $purifier;

    if (isset($cache[$html])) {
      return $cache[$html];
    }

    // Only Unicode alphanum characters and whitespaces
    /*
    if (!preg_match("/[^\p{L}\p{N}\s]/u", $html)) {
      // No need to purify
      return $html;
    }
    */

    if (!$purifier) {
      $root = CAppUI::conf("root_dir");

      if (!class_exists("HTMLPurifier", false) || !class_exists("HTMLPurifier_Config", false)) {
        $file = "$root/lib/htmlpurifier/library/HTMLPurifier.auto.php";
        if (is_readable($file)) {
          include_once $file;
        }
      }

      $config = HTMLPurifier_Config::createDefault();
      // App encoding (in order to prevent from removing diacritics)
      $config->set('Core.Encoding', CApp::$encoding);
      $config->set('Cache.SerializerPath', "$root/tmp");

      $purifier = new HTMLPurifier($config);
    }

    $purified = $purifier->purify($html);

    if (isset($purified[5])) {
      $cache[$html] = $purified;
    }

    return $purified;
  }

  /**
   * PHP-Markdown parser call
   *
   * @param string $text Text to parse
   *
   * @return string
   */
  static function markdown($text) {
    if (trim($text) == "") {
      return $text;
    }

    static $cache = array();

    if (isset($cache[$text])) {
      return $cache[$text];
    }

    if (!class_exists("Parsedown", false)) {
      $root = CAppUI::conf("root_dir");
      $file = "$root/lib/parsedown/Parsedown.php";

      require_once $file;
    }

    $markdown = Parsedown::instance()->set_breaks_enabled(true)->parse($text);

    if (isset($markdown[5])) {
      $cache[$text] = $markdown;
    }

    return $markdown;
  }

  /**
   * nl2br alternative for bull-separated one-line string
   *
   * @param $string
   *
   * @return string
   */
  static function nl2bull($string) {
    return str_replace(array("\r\n", "\n", "\r"), " &bull; ", $string);
  }

  /**
   * Convert a decimal number into a roman literal number
   *
   * @param integer $num The number to convert
   *
   * @return string
   */
  static function dec2roman($num) {
    $vals  = array(1000, 500, 100, 50, 10, 5, 1, 'M' => 1000, 'D' => 500, 'C' => 100, 'L' => 50, 'X' => 10, 'V' => 5, 'I' => 1);
    $chars = array('M', 'D', 'C', 'L', 'X', 'V', 'I');

    if (is_numeric($num) && ($num < 1 || $num > 3999)) {
      return '';
    }

    $str = '';

    for ($x = 0; $x < 7; $x++) {
      $am = floor($num / $vals[$x]);
      $num %= $vals[$x];

      if ($am == 4) { // max is 3 of same
        if (isset($lastChar) && (($lastChar == 'V' && $x == 6) || ($lastChar == 'L' && $x == 4) || ($lastChar == 'D' && $x == 2))) {
          // trying to make 9__
          $str = substr($str, 0, -1) . $chars[$x] . $chars[($x - 2)]; // remove last
        }
        else { // 4__
          $str .= $chars[$x] . $chars[($x - 1)];
        }
        $lastChar = $chars[$x];
      }
      elseif ($am > 0) {
        for ($y = 0; $y < $am; $y++) { // add the chars
          $str .= $chars[$x];
        }
        $lastChar = $chars[$x]; // save last for next loop
      }
    }

    return $str;
  }

  /**
   * Convert a roman literal number in a decimal number
   *
   * @param string $str The roman literal number to convert
   *
   * @return int
   */
  static function roman2dec($str) {
    $vals  = array(1000, 500, 100, 50, 10, 5, 1, 'M' => 1000, 'D' => 500, 'C' => 100, 'L' => 50, 'X' => 10, 'V' => 5, 'I' => 1);
    $chars = array('M', 'D', 'C', 'L', 'X', 'V', 'I');

    if (!is_numeric($str) && !preg_match('/[MDCLXVI]+/i', $str)) {
      return 0;
    }

    $str     = strtoupper($str);
    $arr     = str_split($str);
    $lastVal = 0;
    $num     = 0;

    foreach ($arr as $char) {
      $num += $vals[$char];
      if ($vals[$char] > $lastVal) { // trying to deduct (ex. XC -> 90)
        $num -= (2 * $lastVal); // remove added before this loop AND deduct
      }
      $lastVal = $vals[$char];
    }

    return $num;
  }

  static function splitString($string, $separators, $end, &$positions) {
    $l     = strlen($string);
    $split = array();

    for ($p = 0; $p < $l;) {
      $e = strcspn($string, $separators . $end, $p);
      $e += strspn($string, $separators, $p + $e);
      $split[]     = substr($string, $p, $e);
      $positions[] = $p;
      $p += $e;

      if (strlen($end) && ($e = strspn($string, $end, $p))) {
        $split[]     = substr($string, $p, $e);
        $positions[] = $p;
        $p += $e;
      }
    }

    $positions[] = $p;

    return $split;
  }

  static function diff($before, $after, $mode = "w") {
    switch ($mode) {
      case 'c':
        $lb = strlen($before);
        $la = strlen($after);
        break;

      case 'w':
        $before = self::splitString($before, " \t", "\r\n", $posb);
        $lb     = count($before);
        $after  = self::splitString($after, " \t", "\r\n", $posa);
        $la     = count($after);
        break;

      case 'l':
        $before = self::splitString($before, "\r\n", '', $posb);
        $lb     = count($before);
        $after  = self::splitString($after, "\r\n", '', $posa);
        $la     = count($after);
        break;

      default:
        return false;
    }

    $diff = array();
    for ($b = $a = 0; $b < $lb && $a < $la;) {
      for ($pb = $b; $a < $la && $pb < $lb && $after[$a] === $before[$pb]; ++$a, ++$pb) {
        ;
      }

      if ($pb !== $b) {
        $diff[] = array(
          'change'   => '=',
          'position' => ($mode === 'c' ? $b : $posb[$b]),
          'length'   => ($mode === 'c' ? $pb - $b : $posb[$pb] - $posb[$b])
        );

        $b = $pb;
      }

      if ($b === $lb) {
        break;
      }

      for ($pb = $b; $pb < $lb; ++$pb) {
        for ($pa = $a; $pa < $la && $after[$pa] !== $before[$pb]; ++$pa) {
          ;
        }

        if ($pa !== $la) {
          break;
        }
      }

      if ($pb !== $b) {
        $diff[] = array(
          'change'   => '-',
          'position' => ($mode === 'c' ? $b : $posb[$b]),
          'length'   => ($mode === 'c' ? $pb - $b : $posb[$pb] - $posb[$b])
        );

        $b = $pb;
      }

      if ($pa !== $a) {
        $position = ($mode === 'c' ? $a : $posa[$a]);
        $length   = ($mode === 'c' ? $pa - $a : $posa[$pa] - $posa[$a]);
        $change   = array(
          'change'   => '+',
          'position' => $position,
          'length'   => $length
        );

        $diff[] = $change;
        $a      = $pa;
      }
    }

    if ($a < $la) {
      $position = ($mode === 'c' ? $a : $posa[$a]);
      $length   = ($mode === 'c' ? $la - $a : $posa[$la] - $posa[$a]);
      $change   = array(
        'change'   => '+',
        'position' => $position,
        'length'   => $length
      );

      $diff[] = $change;
    }

    return self::$diff = $diff;
  }

  static function formatDiffAsHTML($before, $after, $mode = "w") {
    if (!self::diff($before, $after, $mode)) {
      return false;
    }

    $html = '';
    $td   = count(self::$diff);

    for ($d = 0; $d < $td; ++$d) {
      $diff = self::$diff[$d];

      switch ($diff['change']) {
        case '=':
          $html .= nl2br(htmlSpecialChars(substr($before, $diff['position'], $diff['length'])));
          break;

        case '-':
          $html .= '<del class="diff">' . nl2br(htmlSpecialChars(substr($before, $diff['position'], $diff['length']))) . '</del>';
          break;

        case '+':
          $html .= '<ins class="diff">' . nl2br(htmlSpecialChars(substr($after, $diff['position'], $diff['length']))) . '</ins>';
          break;

        default:
          self::$error = $diff['change'] . ' is not an expected difference change type';

          return false;
      }
    }

    return self::$diff_html = self::purifyHTML($html);
  }
}
