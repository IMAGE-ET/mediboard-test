<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
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
