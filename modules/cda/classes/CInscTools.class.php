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
 * Outils pour l'INSC
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
    $String_no_accent = CMbString::removeAccents($string);
    $string_char = preg_replace(array("/NBSP/","/\(c\)/","/\(r\)/"), " ", $String_no_accent);
    $normalize   = preg_replace("/([^A-Za-z])/", " ", $string_char);
    return mb_strtoupper($normalize);
  }
}