<?php /* $Id: do_rpu_aed.php 6473 2009-06-24 15:18:19Z lryo $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_ids  = CValue::post("sejour_ids");
$date_monday = CValue::post("date_monday");
$all_rhs     = CValue::post("all_rhs");

$where["sejour_id"] = CSQLDataSource::prepareIn($sejour_ids);
$where["date_monday"] = $all_rhs  ? ">= '$date_monday'" : "= '$date_monday'";

$order = "sejour_id, date_monday";

$rhs = new CRHS;
$rhss = $rhs->loadList($where, $order);
if (count($rhss)) {
	foreach($rhss as $_rhs) {
	  $_rhs->facture = CValue::post("facture");
	  $msg = $_rhs->store();
	  CAppUI::displayMsg($msg, "CRHS-msg-modify"); 
	}
}
else {
	CAppui::setMsg("CRHS.none", UI_MSG_WARNING);
}

echo CAppUI::getMsg();

CApp::rip();

?>