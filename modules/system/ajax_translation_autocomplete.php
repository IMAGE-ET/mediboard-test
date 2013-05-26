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

global $locales;

$keyword = CValue::get("source", "%%");

$resp = array();

foreach ($locales as $key => $val) {
  if (stripos($key, $keyword) !== false) {
    $resp[$key] = $val;
  }
  if (stripos($val, $keyword) !== false) {
    $resp[$key] = $val;
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("trad", $resp);
$smarty->display("inc_translation_autocomplete.tpl");