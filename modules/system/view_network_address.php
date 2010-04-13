<?php /* $Id: view_history.php 7904 2010-01-21 11:39:37Z lryo $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7904 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m, $AppUI;

$start  = CValue::get("start", 0);

$filter = new CUserLog();
$filter->_date_min    = CValue::getOrSession("_date_min");
$filter->_date_max    = CValue::getOrSession("_date_max");
$filter->user_id      = CValue::getOrSession("user_id");
$filter->ip_address   = CValue::getOrSession("ip_address", "255.255.255.255");

$order_col = CValue::getOrSession("order_col", "date_max");
$order_way = CValue::getOrSession("order_way", "DESC");
$order_way_alt = $order_way == "ASC" ? "DESC" : "ASC";

$user = new CMediusers();
$where = array();
$order = "users.user_last_name, users.user_first_name";
$ljoin = array();
$ljoin["users"] = "users.user_id = users_mediboard.user_id";
$listUsers = $user->loadGroupList(array(), $order, null, null, $ljoin);
foreach($listUsers as $_user) {
  $_user->loadRefFunction();
}

$where = array();
$where["ip_address"] = "IS NOT NULL";
if($filter->_date_min) {
  $where[] = "date >= '$filter->_date_min'";
  mbTrace($filter->_date_min);
}
if($filter->_date_max) {
  $where[] = "date <= '$filter->_date_max'";
}
$where[] = "user_id ".CSQLDataSource::prepareIn(array_keys($listUsers), $filter->user_id);
if($filter->ip_address) {
  //$binary_address = inet_pton($filter->ip_address);
  //$binary_address = $filter->ip_address;
  //$where[] = "('$binary_address' & ip_address) = $binary_address";
}
$order = "$order_col $order_way";
$group = null;
$group = array("ip_address");
$ljoin = null;

$total_list_count = $filter->countMultipleList($where, $order, null, $group, $ljoin, array("ip_address", "MAX(date) AS date_max, GROUP_CONCAT(DISTINCT user_id SEPARATOR '|') AS user_list"));
//if($filter->ip_address) {
  foreach($total_list_count as $key => $_log) {
    if(!(inet_ntop($_log["ip_address"]) == inet_ntop($_log["ip_address"] & inet_pton($filter->ip_address)))) {
      unset($total_list_count[$key]);
    }
  }
$total_list = array_slice($total_list_count, $start, 30);
foreach($total_list as &$_log) {
  $_log["ip"]         = inet_ntop($_log["ip_address"]);
  $_log["ip_explode"] = explode(".", $_log["ip"]);
  $list_users = explode("|", $_log["user_list"]);
  $_log["users"] = array();
  foreach($list_users as $_user_id) {
    if(isset($listUsers[$_user_id])) {
      $_log["users"][$_user_id] = $listUsers[$_user_id];
    }
  }
}
$list_count = count($total_list_count);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("start"        , $start        );
$smarty->assign("filter"       , $filter       );
$smarty->assign("listUsers"    , $listUsers    );
$smarty->assign("list_count"   , $list_count   );
$smarty->assign("total_list"   , $total_list   );
$smarty->assign("order_col"    , $order_col    );
$smarty->assign("order_way"    , $order_way    );
$smarty->assign("order_way_alt", $order_way_alt);

$smarty->display("view_network_address.tpl");


?>