<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$keywords = CValue::post("keywords_code");

if ($keywords == '') {
  $keywords = '%%';
}

$code = new CCodeCIM10();
$codes = $code->findCodes($keywords, $keywords, CCodeCIM10::LANG_FR, 6);

$smarty = new CSmartyDP();

$smarty->assign("codes"    , $codes);
$smarty->assign("nodebug"  , true);
$smarty->assign("keywords" , $keywords);
$smarty->assign("sejour_id", CValue::get("sejour_id"));

$smarty->display("inc_code_cim10_autocomplete.tpl");
