<?php 

/**
 * Autocomplete d'un code ccam sur les codes associés
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$code     = CValue::get("code");
$keywords = CValue::post("keywords");

$code_ccam = new CCodeCCAM($code);
$code_ccam->getActesAsso($keywords, 30);

$codes = array();

foreach ($code_ccam->assos as $_code) {
  $_code_value = $_code['code'];
  $codes[$_code_value] = CCodeCCAM::get($_code_value, CCodeCCAM::MEDIUM);
}

$smarty = new CSmartyDP();

$smarty->assign("codes", $codes);
$smarty->assign("keywords", $keywords);
$smarty->assign("nodebug" , true);

$smarty->display("httpreq_do_ccam_autocomplete.tpl");