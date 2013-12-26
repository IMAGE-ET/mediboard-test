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
$date     = CValue::getOrSession("date"    , CMbDT::date());
$groupmod = CValue::getOrSession("groupmod", 2);

// Hour range for daily stats
$hour_min = CValue::getOrSession("hour_min", "6");
$hour_max = CValue::getOrSession("hour_max", "22");
$hours = range(0, 23);

$left_mode      = CValue::getOrSession("left_mode", "request_time"); // request_time, cpu_time, errors, memory_peak
$left_sampling  = CValue::getOrSession("left_sampling", "mean"); // total, mean

$right_mode     = CValue::getOrSession("right_mode", "hits"); // hits, size
$right_sampling = CValue::getOrSession("right_sampling", "total"); // total, mean

$DBorNotDB = CValue::getOrSession("DBorNotDB_hidden", false); // Do we use DB or datasource_logs?

// Human/bot filter
$human_bot = CValue::getOrSession("human_bot", "0");

$module = null;
if (!is_numeric($groupmod)) {
  $module   = $groupmod;
  $groupmod = 0;
}

CAppUI::requireModuleFile('dPstats', 'graph_accesslog');

$to = CMbDT::date("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "one-day")) {
  case "one-day":
    $today = CMbDT::date("-1 DAY", $to);
    // Hours limitation
    $from = CMbDT::dateTime("+$hour_min HOUR", $today);
    $to   = CMbDT::dateTime("+$hour_max HOUR", $today);
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

$logs = CAccessLog::loadAgregation($from, $to, $groupmod, $module, $DBorNotDB, $human_bot);

$graphs = array();
$left   = array($left_mode, $left_sampling);
$right  = array($right_mode, $right_sampling);

if (!$DBorNotDB) {
  foreach ($logs as $log) {
    switch ($groupmod) {
      case 0:
        $graphs[] = graphAccessLog($log->module, $log->action, $from, $to, $interval, $left, $right, $DBorNotDB, $human_bot);
        break;

      case 1:
        $graphs[] = graphAccessLog($log->module, null        , $from, $to, $interval, $left, $right, $DBorNotDB, $human_bot);
        break;

      case 2:
        $graphs[] = graphAccessLog(null        , null        , $from, $to, $interval, $left, $right, $DBorNotDB, $human_bot);
        break;
    }
  }
}
else {
  foreach ($logs as $log) {
    switch ($groupmod) {
      case 0:
        $graph = graphAccessLog($log['module'], $log['action'], $from, $to, $interval, $left, $right, $DBorNotDB, $human_bot);
        if ($graph["series"]) {
          $graphs[] = $graph;
        }
        break;
        
      case 1:
        $graph = graphAccessLog($log['module'], null, $from, $to, $interval, $left, $right, $DBorNotDB, $human_bot);
        if ($graph["series"]) {
          $graphs[] = $graph;
        }
        break;
        
      case 2:
        $graph = graphAccessLog(null, null, $from, $to, $interval, $left, $right, $DBorNotDB, $human_bot);
        if ($graph["series"]) {
          $graphs[] = $graph;
        }
    }
  }
}

CSQLDataSource::$trace = false;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graphs",         $graphs);
$smarty->assign("groupmod",       $groupmod);

$smarty->assign("date",           $date);
$smarty->assign("hours",          $hours);
$smarty->assign("hour_min",       $hour_min);
$smarty->assign("hour_max",       $hour_max);

$smarty->assign("left_mode",      $left_mode);
$smarty->assign("left_sampling",  $left_sampling);

$smarty->assign("right_mode",     $right_mode);
$smarty->assign("right_sampling", $right_sampling);

$smarty->assign("module",         $module);
$smarty->assign("interval",       $interval);
$smarty->assign("listModules",    CModule::getInstalled());

$smarty->assign("DBorNotDB",      $DBorNotDB);
$smarty->assign("human_bot",      $human_bot);

$smarty->display("view_access_logs.tpl");
