<?php
/**
 * Access logging
 *
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id$
 * @link       http://www.mediboard.org
 */

if (CAppUI::conf("readonly") || !CAppUI::conf("log_access")) {
  return;
}

global $m, $action, $dosql;

if (!$action) {
  $action = $dosql;
}

// $action may not defined when the module is inactive
if (!$action) {
  return;
}

// Check prerequisites
$ds = CSQLDataSource::get("std");

$ma         = new CModuleAction();
$ma->module = $m;
$ma->action = $action;

$module_action_id = $ma->getID();

// Key initialisation
$log                   = new CAccessLog();
$log->module_action_id = $module_action_id;

$minutes     = CMbDT::format(null, "%M");
$arr_minutes = (floor($minutes / 10) * 10) % 60;
($arr_minutes === 0) ? $arr_minutes = "00" : null;

// 10 min. long aggregation
$log->period = CMbDT::format(null, "%Y-%m-%d %H:" . $arr_minutes . ":00");

$chrono = CApp::$chrono;

// Stop chrono if not already done
if ($chrono->step > 0) {
  $chrono->stop();
}

// Probe aquisition
$rusage = getrusage();

$log->hits++;
$log->duration    += $chrono->total;
$log->processus   += floatval($rusage["ru_utime.tv_usec"]) / 1000000 + $rusage["ru_utime.tv_sec"];
$log->processor   += floatval($rusage["ru_stime.tv_usec"]) / 1000000 + $rusage["ru_stime.tv_sec"];
$log->request     += $ds->chrono->total;
$log->nb_requests += $ds->chrono->nbSteps;
$log->size        += CApp::getOuputBandwidth();
//$log->other_bandwidth += CApp::getOtherBandwidth();
$log->peak_memory += memory_get_peak_usage();
$log->errors      += CApp::$performance["error"];
$log->warnings    += CApp::$performance["warning"];
$log->notices     += CApp::$performance["notice"];

$log->aggregate = 10;
$log->bot       = CApp::$is_robot ? 1 : 0;

// Fast store
if ($msg = $log->fastStore()) {
  trigger_error($msg, E_USER_WARNING);
  exit();
}

if (CAppUI::conf("log_datasource_metrics")) {
  foreach (CSQLDataSource::$dataSources as $_datasource) {
    if ($_datasource) {
      $dsl                   = new CDataSourceLog();
      $dsl->module_action_id = $log->module_action_id;
      $dsl->datasource       = $_datasource->dsn;
      $dsl->requests         = $_datasource->chrono->nbSteps;
      $dsl->duration         = round(floatval($_datasource->chrono->total), 3);
      $dsl->period           = $log->period;
      $dsl->aggregate        = $log->aggregate;
      $dsl->bot              = $log->bot;

      if ($msg = $dsl->fastStore()) {
        trigger_error($msg, E_USER_WARNING);
      }
    }
  }
}
