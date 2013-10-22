<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$keyword = CValue::get("source", "%%");
//@TODO : utiliser regex
$regex = CValue::get("regex", 0);

$resp = array();

$locales = CAppUI::flattenCachedLocales(CAppUI::$lang);

foreach ($locales as $key => $val) {

  if ($regex) {
    $keyword = "/^$keyword/";

    if (preg_match($keyword, $key)) {
      $resp[$key]["key"] = $key;
      $resp[$key]["val"] = $val;
    }
    if (preg_match($keyword, $val)) {
      $resp[$key]["key"] = $key;
      $resp[$key]["val"] = $val;
    }
  }
  else {
    if (stripos($key, $keyword) !== false) {
      $resp[$key]["key"] = str_replace($keyword, '<span style="text-decoration: underline;">'.$keyword.'</span>', $key);
      $resp[$key]["val"] = str_replace($keyword, '<span style="text-decoration: underline;">'.$keyword.'</span>', $val);
    }
    if (stripos($val, $keyword) !== false) {
      $resp[$key]["key"] = str_replace($keyword, '<span style="text-decoration: underline;">'.$keyword.'</span>', $key);
      $resp[$key]["val"] = str_replace($keyword, '<span style="text-decoration: underline;">'.$keyword.'</span>', $val);
    }
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("trad", $resp);
$smarty->display("inc_translation_autocomplete.tpl");