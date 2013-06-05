<?php

/**
 * Hprim 2.1 utility class declaration
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */


/**
 * Hprim 21 utility class
 */
class CHprim21 {  
  static function getTag($group_id = null) {
    // Pas de tag Identifiant
    if (null == $tag = CAppUI::conf("hprim21 tag")) {
      return null;
    }

    // Permettre des ID en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag);
  }

  static function formatHPRIMBiologie($string) {
    if (substr($string, 0, 2) === "\xFF\xFE") {
      // UTF-16 with BOM
      $string = iconv("UTF-16", "iso-8859-1//TRANSLIT//IGNORE", $string);
    }
    elseif (strpos($string, "\x8E") !== false) {
      // MacRoman
      $string = iconv("macintosh", "iso-8859-1//TRANSLIT//IGNORE", $string);
    }

    $lines = preg_split("/(\r\n|\n)/", $string, 13);

    $lines[10] = preg_split("/\s/", $lines[10], 2); // Expediteur
    $lines[11] = preg_split("/\s/", $lines[11], 2); // Destinataire

    $all_text = $lines[12];
    unset($lines[12]);

    $lab_header = "****LAB****";
    $fin_header = "****FIN****";

    $lab_pos = strpos($all_text, $lab_header);
    $fin_pos = strpos($all_text, $fin_header);

    $text  = substr($all_text, 0, $lab_pos);
    $hprim = trim(substr($all_text, $lab_pos+strlen($lab_header), $fin_pos-$lab_pos-strlen($fin_header)));
    $hprim = preg_split("/(\r\n|\n)/", $hprim);

    $pattern = "/^RES\|".      // 01
      "(?P<label>[^\|]*)\|".   // 02
      "(?P<code>[^\|]*)\|".    // 03
      "(?P<type>[^\|]*)\|".    // 04
      "(?P<value>[^\|]*)\|".   // 05
      "(?P<unit>[^\|]*)\|".    // 06
      "(?P<min>[^\|]*)\|".     // 07
      "(?P<max>[^\|]*)\|".     // 08
      "(?P<anormal>[^\|]*)\|". // 09
      "(?P<status>[^\|]*)\|".  // 10
      "(?P<value2>[^\|]*)\|".  // 11
      "(?P<unit2>[^\|]*)\|".   // 12
      "(?P<min2>[^\|]*)\|".    // 13
      "(?P<max2>[^\|]*)\|".    // 14
      "/";

    $anormalites = array(
      "L" => "inférieur à la normale",
      "H" => "supérieur à la normale",
      "LL" => "inférieur à la valeur panique basse",
      "HH" => "supérieur à la valeur panique haute",
      "N" => "normal",
    );

    $classes = array(
      "L"  => "warning",
      "H"  => "warning",
      "LL" => "error",
      "HH" => "error",
      "N"  => "",
    );

    $results = array();
    foreach ($hprim as $_hprim) {
      $matches = array();
      if (preg_match($pattern, $_hprim, $matches)) {
        $matches["anormal_text"]  = CValue::read($anormalites, $matches["anormal"]);
        $matches["anormal_class"] = CValue::read($classes, $matches["anormal"]);

        $results[] = $matches;
      }
    }

    $template = new CSmartyDP("modules/hprim21");
    $template->assign("header",  $lines);
    $template->assign("text",    $text);
    $template->assign("results", $results);

    return $template->fetch("inc_hprim_biologie_results.tpl");
  }
}
