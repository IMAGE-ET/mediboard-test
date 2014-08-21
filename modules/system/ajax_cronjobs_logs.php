<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$status     = CValue::get("status");
$error      = CValue::get("error");
$cronjob_id = CValue::get("cronjob_id");
$date_min   = CValue::get("_date_min");
$date_max   = CValue::get("_date_max");
$page       = (int)CValue::get("page", 0);
$where      = array();

if ($status) {
  $where["status"] = "= '$status'";
}
if ($error) {
  $where["error"] = "LIKE '%$error%'";
}
if ($cronjob_id) {
  $where["cronjob_id"] = "= '$cronjob_id'";
}
if ($date_min) {
  $where["start_datetime"] = ">= '$date_min'";
}
if ($date_max) {
  $where["start_datetime"] = $date_min ? $where["start_datetime"]."AND start_datetime <= '$date_max'" : "<= '$date_max'";
}

$log    = new CCronJobLog();
/** @var CCronJobLog[] $logs */
$nb_log = $log->countList($where);
$logs   = $log->loadList($where, "start_datetime DESC", "$page, 30");

$cronjobs = CCronJobLog::massLoadFwdRef($logs, "cronjob_id");
foreach ($logs as $_log) {
  $_log->_ref_cronjob = CMbArray::get($cronjobs, $_log->cronjob_id);
}

$smarty = new CSmartyDP();
$smarty->assign("logs"  , $logs);
$smarty->assign("page"  , $page);
$smarty->assign("nb_log", $nb_log);
$smarty->display("inc_cronjobs_logs.tpl");