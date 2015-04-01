<?php

/**
 * $Id$
 *
 * @category Developpement
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
$datetime_min  = CValue::getOrSession("_datetime_min", CMbDT::dateTime('-1 WEEK'));
$datetime_max  = CValue::getOrSession("_datetime_max");
$order_by      = CValue::getOrSession("order_by");
$group_similar = CValue::getOrSession("group_similar", "similar");
$user_id       = CValue::getOrSession("user_id");
$human         = CValue::getOrSession("human");
$robot         = CValue::getOrSession("robot");

$error_log = new CErrorLog();
$error_log->text = $text;
$error_log->server_ip = $server_ip;
$error_log->_datetime_min = $datetime_min;
$error_log->_datetime_max = $datetime_max;

$log_size = filesize(CError::LOG_PATH);
$log_size_limit = CError::LOG_SIZE_LIMIT;

$offset = -1;
if ($log_size > $log_size_limit) {
  $offset = $log_size - $log_size_limit;
}
$log_content = file_get_contents(CError::LOG_PATH, false, null, $offset);

// Debug file
$debug_content = null;
$debug_size    = 0;
if (file_exists(CMbDebug::DEBUG_PATH)) {
  $debug_size = filesize(CMbDebug::DEBUG_PATH);
  $debug_size_limit = CMbDebug::DEBUG_SIZE_LIMIT;
  $offset = -1;
  if ($debug_size > $debug_size_limit) {
    $offset = $debug_size - $debug_size_limit;
  }
  $debug_content = file_get_contents(CMbDebug::DEBUG_PATH, false, null, $offset);
}

// Récupération de la liste des utilisateurs disponibles
$user = new CUser();
$user->template = "0";
$order = "user_last_name, user_first_name";
$list_users = $user->loadMatchingList($order);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("log",           $log_content);
$smarty->assign("log_size",      CMbString::toDecaBinary($log_size));
$smarty->assign("debug",         $debug_content);
$smarty->assign("debug_size",    CMbString::toDecaBinary($debug_size));
$smarty->assign("error_log",     $error_log);
$smarty->assign("error_type",    $error_type);
$smarty->assign("server_ip",     $server_ip);
$smarty->assign("order_by",      $order_by);
$smarty->assign("group_similar", $group_similar);
$smarty->assign("error_types",   CError::getErrorTypesByCategory());
$smarty->assign("user_id",       $user_id);
$smarty->assign("list_users",    $list_users);
$smarty->assign("human",         $human);
$smarty->assign("robot",         $robot);
$smarty->display('view_logs.tpl');
