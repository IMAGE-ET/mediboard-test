<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
$sejour_ids  = CValue::post("sejour_ids");
$date_monday = CValue::post("date_monday");
$all_rhs     = CValue::post("all_rhs");

$where["sejour_id"] = CSQLDataSource::prepareIn($sejour_ids);
$where["date_monday"] = $all_rhs  ? ">= '$date_monday'" : "= '$date_monday'";

$order = "sejour_id, date_monday";

$rhs = new CRHS;
/** @var CRHS[] $rhss */
$rhss = $rhs->loadList($where, $order);
if (count($rhss)) {
  foreach ($rhss as $_rhs) {
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
