<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$type     = CValue::get("type");
$keywords = "";

switch($type) {
	case "editDP":
		         $keywords = CValue::post("keywords_code_dp");
	           break;
	case "editDR":
		         $keywords = CValue::post("keywords_code_dr");
	           break;
	case "editDA":
		         $keywords = CValue::post("keywords_code_added");
	           break;
	default:   
	           $keywords = CValue::post("keywords_code");
}

if ($keywords == '') $keywords = '%%';

$code = new CCodeCIM10;
$codes = $code->findCodes($keywords, '', CCodeCIM10::LANG_FR, 5);

$smarty = new CSmartyDP();

$smarty->assign("codes"   , $codes);
$smarty->assign("nodebug" , true);
$smarty->assign("keywords", $keywords);
$smarty->assign("type"    , $type);
$smarty->assign("sejour_id", CValue::get("sejour_id"));
$smarty->display("inc_code_cim10_autocomplete.tpl");

?>