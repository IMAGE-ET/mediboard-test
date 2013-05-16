<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * CInscTools
 */
class CInscTools {

  /**
   * Formate la chaine pour l'INSC
   *
   * @param String $string String
   *
   * @return String
   */
  static function formatString($string) {
    $String_no_accent = self::removeAccents($string);
    $string_char = preg_replace(array("/NBSP/","/\(c\)/","/\(r\)/"), " ", $String_no_accent);
    $normalize = preg_replace("/([^A-Za-z])/", " ", $string_char);
    return mb_strtoupper($normalize);
  }

  /**
   * Retourne la chaine(UTF-8/ISO-8859-1) sans accent
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
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
        chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
        chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
        chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
        chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
        chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 'b', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(166) => 'ae',
        chr(195).chr(167) => 'c', chr(195).chr(168) => 'e',
        chr(195).chr(169) => 'e', chr(195).chr(170) => 'e',
        chr(195).chr(171) => 'e', chr(195).chr(172) => 'i',
        chr(195).chr(173) => 'i', chr(195).chr(174) => 'i',
        chr(195).chr(175) => 'i', chr(195).chr(176) => 'd',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(184) => 'Y', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z',);

      $string = strtr($string, $chars);
    }
    else {
      // Assume ISO-8859-1 if not UTF-8
      $chars['in'] = chr(138).chr(142).chr(154).chr(158)
        .chr(159).chr(192).chr(193).chr(194)
        .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
        .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
        .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
        .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
        .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
        .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
        .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
        .chr(252).chr(253).chr(255);

      $chars['out'] = "SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

      $string = strtr($string, $chars['in'], $chars['out']);
      $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(223), chr(230), chr(240));
      $double_chars['out'] = array('OE', 'oe', 'AE', 'D', 'b', 'ae', 'd');
      $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
  }

  /**
   * Vérifie que la chaine est en UTF8
   *
   * @param String $str String
   *
   * @return bool
   */
  static function seemsUtf8($str)
  {
    $length = strlen($str);
    for ($i=0; $i < $length; $i++) {
      $c = ord($str[$i]);
      if ($c < 0x80) {
        $n = 0;
      } //0bbbbbbb
      elseif (($c & 0xE0) == 0xC0) $n=1; //110bbbbb
      elseif (($c & 0xF0) == 0xE0) $n=2; //1110bbbb
      elseif (($c & 0xF8) == 0xF0) $n=3; //11110bbb
      elseif (($c & 0xFC) == 0xF8) $n=4; //111110bb
      elseif (($c & 0xFE) == 0xFC) $n=5; //1111110b
      else {
        return false;
      } //Does not match any model
      for ($j=0; $j<$n; $j++) { //n bytes matching 10bbbbbb follow ?
        if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
          return false;
        }
      }
    }
    return true;
  }
}
