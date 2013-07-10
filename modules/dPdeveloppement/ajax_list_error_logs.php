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

$start          = (int) CValue::get("start", 0);

$error_type     = CValue::get("error_type");
$server_ip      = CValue::get("server_ip");
$group_similar  = CValue::get("group_similar", 1);

CValue::setSession("error_type",    $error_type);
CValue::setSession("server_ip",     $server_ip);
CValue::setSession("group_similar", $group_similar);

$where = array();

$error_log = new CErrorLog();
$spec = $error_log->_spec;
$ds = $spec->ds;

if (!empty($error_type)) {
  $error_type = array_keys($error_type);
  $where["error_type"] = $ds->prepareIn($error_type);
}

if ($server_ip) {
  $where["server_ip"] = $ds->prepareLike($server_ip);
}

$order = "datetime DESC, $spec->key DESC";
$limit = "$start, 30";

$groupby = null;
$error_logs_similar = array();

if ($group_similar) {
  $groupby = "text, DATE_FORMAT(datetime, '%Y-%m-%d %H:00:00'), user_id, server_ip, stacktrace_id, param_GET_id, param_POST_id";

  $request = new CRequest();
  $request->addWhere($where);
  $request->addOrder($order);
  $request->addGroup($groupby);
  $request->setLimit($limit);

  $fields = array(
    "GROUP_CONCAT(error_log_id) AS similar_ids"
  );
  $error_logs_similar = $ds->loadList($request->getCountRequest($error_log, $fields));

  $request->setLimit(null);
  $total = count($ds->loadList($request->getCountRequest($error_log, $fields)));
}
else {
  $total = $error_log->countList($where);
}

/** @var CErrorLog[] $error_logs */
$error_logs = $error_log->loadList($where, $order, $limit, $groupby);

foreach ($error_logs as $_error_log) {
  $_error_log->loadComplete();
}

$error_logs = array_values($error_logs);

if ($group_similar) {
  foreach ($error_logs as $_i => $_error_log) {
    $_error_log->_similar_count = $error_logs_similar[$_i]["total"];
    $_error_log->_similar_ids   = explode(",", $error_logs_similar[$_i]["similar_ids"]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("error_logs", $error_logs);
$smarty->assign("total", $total);
$smarty->assign("start", $start);
$smarty->assign("group_similar", $group_similar);
$smarty->display('inc_list_error_logs.tpl');
