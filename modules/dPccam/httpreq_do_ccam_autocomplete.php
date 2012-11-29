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
  $_code_value = $_code["CODE"];
  $codes[$_code_value] = CCodeCCAM::get($_code_value, CCodeCCAM::MEDIUM);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->debugging = false;

$smarty->assign("keywords", $keywords);
$smarty->assign("codes"   , $codes);
$smarty->assign("nodebug" , true);

$smarty->display("httpreq_do_ccam_autocomplete.tpl");
