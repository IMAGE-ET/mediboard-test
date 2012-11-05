<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */
 
CCanDo::checkRead();

$_date_min = CValue::getOrSession("_date_min", mbDateTime("-7 day"));
$_date_max = CValue::getOrSession("_date_max", mbDateTime("+1 day"));
$group_id  = CValue::getOrSession("group_id", CGroups::loadCurrent()->_id);
$sender_id  = CValue::getOrSession("sender_id");
$receiver_id  = CValue::getOrSession("receiver_id");
$status  = CValue::getOrSession("status");

$page_number      = CValue::getOrSession("page_number", 0);
$order_col = CValue::getOrSession("order_col");
$order_way = CValue::getOrSession("order_way");

$session = new CDicomSession();

$where = array();
if ($group_id) {
  $where["group_id"] = " = '$group_id'";
}

if ($receiver_id) {
  $where["receiver_id"] = " = '$receiver_id'";
}

if ($sender_id) {
  $where["sender_id"] = " = '$sender_id'";
}

if ($status) {
  $where["status"] = " = '$status'";
}

if ($_date_min && $_date_max) {
  $where["begin_date"] = " BETWEEN '$_date_min' AND '$_date_max'";
}

$order = "$order_col $order_way";
$index[] = "begin_date";

$sessions = $session->loadList($where, $order, "$page_number, 20", null, null, $index);
$total_sessions = count($sessions);

foreach ($sessions as $_session) {
  $_session->loadRefGroups();
  $_session->loadRefActor();
  $_session->updateFormFields();
}

$session = new CDicomSession;
$session->group_id = $group_id;

$smarty = new CSmartyDP();

$smarty->assign("session", $session);
$smarty->assign("sessions", $sessions);
$smarty->assign("total_sessions", $total_sessions);
$smarty->assign("page_number", $page_number);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);

$smarty->display("inc_sessions.tpl");
?>