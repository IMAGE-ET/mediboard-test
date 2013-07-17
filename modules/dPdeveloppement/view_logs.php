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

CCanDo::checkRead();

$error_type    = CValue::getOrSession("error_type", array());
$text          = CValue::getOrSession("text");
$server_ip     = CValue::getOrSession("server_ip");
$datetime_min  = CValue::getOrSession("_datetime_min");
$datetime_max  = CValue::getOrSession("_datetime_max");
$order_by      = CValue::getOrSession("order_by");
$group_similar = CValue::getOrSession("group_similar", 1);

$error_log = new CErrorLog();
$error_log->text = $text;
$error_log->server_ip = $server_ip;
$error_log->_datetime_min = $datetime_min;
$error_log->_datetime_max = $datetime_max;

$log_size = filesize(LOG_PATH);
$log_size_limit = 1024*1024*2;

$offset = -1;
if ($log_size > $log_size_limit) {
  $offset = $log_size - $log_size_limit;
}
$log_content = file_get_contents(LOG_PATH, false, null, $offset);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("log",           $log_content);
$smarty->assign("log_size",      CMbString::toDecaBinary(strlen($log_content)));
$smarty->assign("error_log",     $error_log);
$smarty->assign("error_type",    $error_type);
$smarty->assign("server_ip",     $server_ip);
$smarty->assign("order_by",      $order_by);
$smarty->assign("group_similar", $group_similar);
$smarty->assign("error_types",   CError::getErrorTypesByCategory());
$smarty->display('view_logs.tpl');
