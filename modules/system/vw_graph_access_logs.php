<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date     = CValue::getOrSession("date", CMbDT::date());
$groupmod = CValue::getOrSession("groupmod", 2);

// Hour range for daily stats
$hour_min = CValue::getOrSession("hour_min", "6");
$hour_max = CValue::getOrSession("hour_max", "22");

// request_time, cpu_time, errors, memory_peak
$left_mode = CValue::getOrSession("left_mode", "request_time");

// total, mean
$left_sampling = CValue::getOrSession("left_sampling", "mean");

// hits, size
$right_mode = CValue::getOrSession("right_mode", "hits");

// total, mean
$right_sampling = CValue::getOrSession("right_sampling", "total");

// Human/bot filter
$human_bot = CValue::getOrSession("human_bot", "0");

CView::enforceSlave();

$hours    = range(0, 24);

$module = null;
if (!is_numeric($groupmod)) {
  $module   = $groupmod;
  $groupmod = 0;
}

$to = CMbDT::date("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "one-day")) {
  case "one-day":
    $today = CMbDT::date("-1 DAY", $to);
    // Hours limitation
    $from = CMbDT::dateTime("+$hour_min HOUR", $today);
    $to   = CMbDT::dateTime("+$hour_max HOUR -1 MINUTE", $today);
    break;

  case "one-week":
    $from = CMbDT::date("-1 WEEK", $to);
    break;

  case "height-weeks":
    $from = CMbDT::date("-8 WEEKS", $to);
    break;

  case "one-year":
    $from = CMbDT::date("-1 YEAR", $to);
    break;

  case "four-years":
    $from = CMbDT::date("-4 YEARS", $to);
    break;

  case "twenty-years":
    $from = CMbDT::date("-20 YEARS", $to);
    break;
}

$graphs = array();
$left   = array($left_mode, $left_sampling);
$right  = array($right_mode, $right_sampling);

switch ($groupmod) {
  case 0:
  case 1:
    $access_logs  = CAccessLog::loadAggregation($from, $to, $groupmod, $module, $human_bot);
    $archive_logs = CAccessLogArchive::loadAggregation($from, $to, $groupmod, $module, $human_bot);
    $logs = array_merge($access_logs, $archive_logs);
    break;

  case 2:
    $logs = array(new CAccessLog());
    break;

  default:
    return;
}

$graphs_by_module = array();
foreach ($logs as $log) {
  switch ($groupmod) {
    case 0:
      $_graph = call_user_func("{$log->_class}::graphAccessLog", $log->_module, $log->_action, $from, $to, $interval, $left, $right, $human_bot);

      if (!isset($graphs_by_module[$log->_module . "-" . $log->_action])) {
        // 1st iteration => graph initialisation
        $graphs_by_module[$log->_module . "-" . $log->_action] = $_graph;
      }
      else {
        // Merging of module-action series and datetime_by_index
        foreach ($_graph["series"] as $_k1 => $_serie) {
          foreach ($_graph["series"][$_k1]["data"] as $_k2 => $_data) {
            $graphs_by_module[$log->_module . "-" . $log->_action]["series"][$_k1]["data"][$_k2][1] += $_data[1];
          }
        }
        $graphs_by_module[$log->_module . "-" . $log->_action]["datetime_by_index"] += $_graph["datetime_by_index"];
      }
      break;

    case 1:
      $_graph = call_user_func("{$log->_class}::graphAccessLog", $log->_module, null, $from, $to, $interval, $left, $right, $human_bot);

      if (!isset($graphs_by_module[$log->_module])) {
        // 1st iteration => graph initialisation
        $graphs_by_module[$log->_module] = $_graph;
      }
      else {
        // Merging of module series and datetime_by_index
        foreach ($_graph["series"] as $_k1 => $_serie) {
          foreach ($_graph["series"][$_k1]["data"] as $_k2 => $_data) {
            $graphs_by_module[$log->_module]["series"][$_k1]["data"][$_k2][1] += $_data[1];
          }
        }
        $graphs_by_module[$log->_module]["datetime_by_index"] += $_graph["datetime_by_index"];
      }
      break;

    case 2:
      $_graph         = CAccessLog::graphAccessLog(null, null, $from, $to, $interval, $left, $right, $human_bot);
      $_archive_graph = CAccessLogArchive::graphAccessLog(null, null, $from, $to, $interval, $left, $right, $human_bot);

      // Merging of series and datetime_by_index
      foreach ($_archive_graph["series"] as $_k1 => $_serie) {
        foreach ($_archive_graph["series"][$_k1]["data"] as $_k2 => $_data) {
          $_graph["series"][$_k1]["data"][$_k2][1] += $_data[1];
        }
      }
      $_graph["datetime_by_index"] += $_archive_graph["datetime_by_index"];

      $graphs[] = $_graph;
      break;
  }
}

switch ($groupmod) {
  case 0:
  case 1:
    $graphs = array();
    foreach ($graphs_by_module as $_graph) {
      $graphs[] = $_graph;
    }
    break;
}

// Ajustements cosmétiques
foreach ($graphs as &$_graph) {
  foreach ($_graph["series"] as &$_series) {
    if (isset($_series["lines"])) {
      $_series["points"] = array(
        "show" => true,
        "radius" => 2,
        "lineWidth" => 1,
      );
    }

    foreach ($_series["data"] as  &$_data) {
      if ($_data[1] === 0) {
        $_data[1] = null;
      }
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->assign("groupmod", $groupmod);
$smarty->display("vw_graph_access_logs.tpl");
