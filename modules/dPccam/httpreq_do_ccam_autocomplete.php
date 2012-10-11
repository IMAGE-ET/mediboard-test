<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$keywords = CValue::request("_codes_ccam", "%%");

$codes = array();
$code = new CCodeCCAM();
foreach ($code->findCodes($keywords, $keywords) as $_code) {
  $codes[$_code["CODE"]] = CCodeCCAM::get($_code["CODE"], CCodeCCAM::MEDIUM);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->debugging = false;

$smarty->assign("keywords"  , $keywords);
$smarty->assign("codes"    , $codes);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_ccam_autocomplete.tpl");


?>