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

$start         = (int) CValue::get("start", 0);

$error_type    = CValue::get("error_type");
$text          = CValue::get("text");
$server_ip     = CValue::get("server_ip");
$datetime_min  = CValue::get("_datetime_min");
$datetime_max  = CValue::get("_datetime_max");
$order_by      = CValue::get("order_by");
$group_similar = CValue::get("group_similar", "similar");
$user_id       = CValue::get("user_id");
$human         = CValue::get("human");
$robot         = CValue::get("robot");

CValue::setSession("error_type",    $error_type);
CValue::setSession("text",          $text);
CValue::setSession("server_ip",     $server_ip);
CValue::setSession("_datetime_min", $datetime_min);
CValue::setSession("_datetime_max", $datetime_max);
CValue::setSession("order_by",      $order_by);
CValue::setSession("group_similar", $group_similar);
CValue::setSession("user_id",       $user_id);
CValue::setSession("human",         $human);
CValue::setSession("robot",         $robot);

$where = array();

$error_log = new CErrorLog();
$spec = $error_log->_spec;
$ds = $spec->ds;

if (($human || $robot) && !($human && $robot)) {
  $tag = CMediusers::getTagSoftware();

  $robots = array();

  if ($tag) {
    $query = "SELECT users.user_id
            FROM users
            LEFT JOIN id_sante400 ON users.user_id = id_sante400.object_id
            WHERE (id_sante400.object_class = 'CMediusers'
              AND id_sante400.tag = ?)
              OR users.dont_log_connection = '1'
            GROUP BY users.user_id";

    $query = $ds->prepare($query, $tag);
  }
  else {
    $query = "SELECT users.user_id
            FROM users
            WHERE users.dont_log_connection = '1'";
  }

  $robots = $ds->loadColumn($query);
}

if ($human && !$robot) {
  if (count($robots)) {
    $where["user_id"] = $ds->prepareNotIn($robots);
  }
}

if ($robot && !$human) {
  if (count($robots)) {
    $where["user_id"] = $ds->prepareIn($robots);
  }
}

if (!empty($error_type)) {
  $error_type = array_keys($error_type);
  $where["error_type"] = $ds->prepareIn($error_type);
}

if ($user_id) {
  $where["user_id"] = $ds->prepareLike($user_id);
}

if ($server_ip) {
  $where["server_ip"] = $ds->prepareLike($server_ip);
}

if ($text) {
  $where["text"] = $ds->prepareLike("%$text%");
}

if ($datetime_min) {
  $where[] = $ds->prepare("datetime >= %", $datetime_min);
}

if ($datetime_max) {
  $where[] = $ds->prepare("datetime <= %", $datetime_max);
}

if ($server_ip) {
  $where["server_ip"] = $ds->prepareLike($server_ip);
}

$order = array();
if ($order_by == "quantity" && ($group_similar && $group_similar !== 'no')) {
  $order[] = "total DESC";
}
$order[] = "datetime DESC";
$order[] = "$spec->key DESC";
$limit = "$start, 30";

$groupby = null;
$error_logs_similar = array();

if ($group_similar && $group_similar !== 'no') {
  if ($group_similar === 'signature') {
    $groupby = "DATE_FORMAT(datetime, '%Y-%m-%d %H:00:00'), user_id, server_ip, signature_hash";
  }
  elseif ($group_similar === 'similar') {
    $groupby = "text, DATE_FORMAT(datetime, '%Y-%m-%d %H:00:00'), user_id, server_ip, stacktrace_id, param_GET_id, param_POST_id";
  }

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

  /** @var CErrorLog[] $error_logs */
  $error_logs = array();
  foreach ($error_logs_similar as $_info) {
    $ids = explode(",", $_info["similar_ids"], 2);

    $log = new CErrorLog();
    $log->load(end($ids));

    $error_logs[] = $log;
  }
}
else {
  $total = $error_log->countList($where);

  /** @var CErrorLog[] $error_logs */
  $error_logs = $error_log->loadList($where, $order, $limit, $groupby);
}

foreach ($error_logs as $_error_log) {
  $_error_log->loadComplete();
}

$error_logs = array_values($error_logs);

$list_ids = array();
if ($group_similar && $group_similar !== 'no') {
  foreach ($error_logs as $_i => $_error_log) {
    $_error_log->_similar_count = $error_logs_similar[$_i]["total"];
    $_error_log->_similar_ids   = explode(",", $error_logs_similar[$_i]["similar_ids"]);

    $list_ids = array_merge($list_ids, $_error_log->_similar_ids);
  }
}
elseif ($group_similar === 'no') {
  $list_ids = CMbArray::pluck($error_logs, "_id");
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("error_logs",    $error_logs);
$smarty->assign("list_ids",      $list_ids);
$smarty->assign("total",         $total);
$smarty->assign("start",         $start);
$smarty->assign("group_similar", $group_similar);
$smarty->display('inc_list_error_logs.tpl');
