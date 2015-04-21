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

CView::enforceSlave();

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
$list_ids = array();
/** @var CErrorLog[] $error_logs */
$error_logs = array();
/** @var CUser[] $users */
$users = array();

if ($group_similar && $group_similar !== 'no') {
  if ($group_similar === 'signature') {
    $groupby = "signature_hash";
  }
  if ($group_similar === 'similar') {
    $groupby = "text, stacktrace_id, param_GET_id, param_POST_id";
  }

  $request = new CRequest();
  $request->addWhere($where);
  $request->addOrder($order);
  $request->addGroup($groupby);
  $request->setLimit($limit);

  $fields = array(
    "GROUP_CONCAT(error_log_id) AS similar_ids",
    "GROUP_CONCAT(user_id)      AS similar_user_ids",
    "GROUP_CONCAT(server_ip)    AS similar_server_ips",
    "MIN(datetime) AS datetime_min",
    "MAX(datetime) AS datetime_max",
  );

  $error_logs_similar = $ds->loadList($request->makeSelectCount($error_log, $fields));

  $request->setLimit(null);
  $total = count($ds->loadList($request->makeSelectCount($error_log, $fields)));

  $user_ids = array();
  foreach ($error_logs_similar as $_info) {
    $similar_ids = explode(",", $_info["similar_ids"]);

    $error_log = new CErrorLog();
    $error_log->load(reset($similar_ids));
    $error_log->_similar_ids        = $similar_ids;
    $error_log->_similar_count      = $_info["total"];
    $error_log->_datetime_min       = $_info["datetime_min"];
    $error_log->_datetime_max       = $_info["datetime_max"];
    $error_log->_similar_user_ids   = array_unique(explode(",", $_info["similar_user_ids"]));
    $error_log->_similar_server_ips = array_unique(explode(",", $_info["similar_server_ips"]));
    $error_logs[] = $error_log;

    $user_ids = array_merge($user_ids, $error_log->_similar_user_ids);
    $list_ids = array_merge($list_ids, $error_log->_similar_ids);
  }

  // Load users for similar groupings
  $user_ids = array_unique($user_ids);
  $user = new CUser();
  $users = $user->loadAll($user_ids);
}
else {
  $total = $error_log->countList($where);
  $error_logs = $error_log->loadList($where, $order, $limit, $groupby);
  $list_ids = CMbArray::pluck($error_logs, "_id");
  $users = CStoredObject::massLoadFwdRef($error_logs, "user_id");
}

// Get all data
CStoredObject::massLoadFwdRef($error_logs, "stacktrace_id");
CStoredObject::massLoadFwdRef($error_logs, "param_GET_id");
CStoredObject::massLoadFwdRef($error_logs, "param_POST_id");
CStoredObject::massLoadFwdRef($error_logs, "session_data_id");
foreach ($error_logs as $_error_log) {
  $_error_log->loadComplete();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("error_logs",    $error_logs);
$smarty->assign("list_ids",      $list_ids);
$smarty->assign("total",         $total);
$smarty->assign("start",         $start);
$smarty->assign("users",         $users);
$smarty->assign("group_similar", $group_similar);
$smarty->display('inc_list_error_logs.tpl');
