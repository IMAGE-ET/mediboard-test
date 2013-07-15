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

$start  = CValue::get("start", 0);

$filter = new CLongRequestLog();
$filter->_date_min = CValue::get("_date_min");
$filter->_date_max = CValue::get("_date_max");
$filter->user_id   = CValue::get("user_id");

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

$order = "datetime DESC";

/** @var CLongRequestLog[] $logs */
$logs = $filter->loadList($where, $order, "$start, 50");
$list_count = $filter->countList($where);

$smarty = new CSmartyDP();

$smarty->assign("start",      $start);
$smarty->assign("list_count", $list_count);
$smarty->assign("filter",     $filter);
$smarty->assign("logs",       $logs);

$smarty->display("inc_list_long_request_logs.tpl");