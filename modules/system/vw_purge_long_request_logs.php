<?php

/**
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$log = new CLongRequestLog();

$log->_date_min   = CValue::get("_date_min", CMbDt::date("-1 MONTH") . ' 00:00:00');
$log->_date_max   = CValue::get("_date_max");
$log->user_id     = CValue::get("user_id");
$log->duration    = CValue::get("duration");
$duration_operand = CValue::get("duration_operand");

$user           = new CUser();
$user->template = "0";
$order          = "user_last_name, user_first_name";
$user_list      = $user->loadMatchingList($order);

$smarty = new CSmartyDP();
$smarty->assign("user_list",        $user_list);
$smarty->assign("log",              $log);
$smarty->assign("duration_operand", $duration_operand);
$smarty->display("vw_purge_long_request_logs.tpl");