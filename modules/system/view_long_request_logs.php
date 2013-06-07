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

$ds = CSQLDataSource::get('std');
$start  = CValue::get("start", 0);

$filter = new CLongRequestLog();

$filter->_date_min = CValue::get("_date_min");
$filter->_date_max = CValue::get("_date_max");
$filter->user_id   = CValue::get("user_id");

// Récupération de la liste des utilisateurs disponibles
$user = new CUser;
$user->template = "0";
$order = "user_last_name, user_first_name";
$user_list = $user->loadMatchingList($order);

// Récupération de la liste des requêtes longues correspondantes
$where = array();
if ($filter->user_id) {
  $where["user_id"] = "= '$filter->user_id'";
}
if ($filter->_date_min) {
  $where[] = "datetime >= '$filter->_date_min'";
}
if ($filter->_date_max) {
  $where[] = "datetime <= '$filter->_date_max'";
}

$log   = new CLongRequestLog();
$order = "datetime DESC";
$long_request_logs = $log->loadList($where, $order, "$start, 50");
foreach ($long_request_logs as $_log) {
  $_log->_query_params_get  = json_decode($_log->query_params_get, true);
  $_log->_query_params_post = json_decode($_log->query_params_post, true);
  $_log->_session_data      = json_decode($_log->session_data, true);

  $_log->getLink();
}

$list_count = $log->countList($where);

$smarty = new CSmartyDP();
$smarty->assign("start",      $start);
$smarty->assign("list_count", $list_count);
$smarty->assign("user_list",  $user_list);
$smarty->assign("filter",     $filter);
$smarty->assign("logs",       $long_request_logs);
$smarty->display("view_long_request_logs.tpl");