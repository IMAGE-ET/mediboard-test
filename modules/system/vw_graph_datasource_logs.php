<?php
/**
 * $Id: view_access_logs.php 22996 2014-04-30 08:54:00Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 22996 $
 */

CCanDo::checkRead();

$date     = CValue::getOrSession("date", CMbDT::date());
$groupmod = CValue::getOrSession("groupmod", 2);

// Hour range for daily stats
$hour_min = CValue::getOrSession("hour_min", "6");
$hour_max = CValue::getOrSession("hour_max", "22");
$hours    = range(0, 24);

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

$module = null;
if (!is_numeric($groupmod)) {
  $module   = $groupmod;
  $groupmod = 0;
}

$to = CMbDT::date("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "one-day")) {
  default:
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

$logs = CDataSourceLog::loadAggregation($from, $to, $groupmod, $module, $human_bot);

foreach ($logs as $log) {
  switch ($groupmod) {
    case 0:
      $graph = CDataSourceLog::graphDataSourceLog($log->_module, $log->_action, $from, $to, $interval, $left, $right, $human_bot);
      if ($graph["series"]) {
        $graphs[] = $graph;
      }
      break;

    case 1:
      $graph = CDataSourceLog::graphDataSourceLog($log->_module, null, $from, $to, $interval, $left, $right, $human_bot);
      if ($graph["series"]) {
        $graphs[] = $graph;
      }
      break;

    case 2:
      $graph = CDataSourceLog::graphDataSourceLog(null, null, $from, $to, $interval, $left, $right, $human_bot);
      if ($graph["series"]) {
        $graphs[] = $graph;
      }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->assign("groupmod", $groupmod);
$smarty->display("vw_graph_datasource_logs.tpl");
