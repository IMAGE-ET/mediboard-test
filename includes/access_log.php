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

// Key initialisation
$log                   = new CAccessLog();
$log->module_action_id = CModuleAction::getID($m, $action);

// 10-minutes period aggregation
// Don't CMbDT::datetime() to get rid of CAppUI::conf("system_date") if ever used
$period = strftime("%Y-%m-%d %H:%M:00");
$period[15] = "0";
$log->period = $period;

// 10 minutes granularity
$log->aggregate = 10;

// One hit
$log->hits++;

// Keep the scalar conversion
$log->bot       = CApp::$is_robot ? 1 : 0;

// Stop chrono if not already done
$chrono = CApp::$chrono;
if ($chrono->step > 0) {
  $chrono->stop();
}
$log->duration    += round(floatval($chrono->total), 3);;

// System probes
$rusage = getrusage();
$log->processus   += round(floatval($rusage["ru_utime.tv_usec"]) / 1000000 + $rusage["ru_utime.tv_sec"], 3);
$log->processor   += round(floatval($rusage["ru_stime.tv_usec"]) / 1000000 + $rusage["ru_stime.tv_sec"], 3);
$log->peak_memory += memory_get_peak_usage();

// SQL stats
$log->request     += round(floatval($ds->chrono->total), 3);
$log->nb_requests += $ds->chrono->nbSteps;

// Bandwidth
$log->size        += CApp::getOuputBandwidth();
//$log->other_bandwidth += CApp::getOtherBandwidth();

// Error log stats
$log->errors      += CApp::$performance["error"];
$log->warnings    += CApp::$performance["warning"];
$log->notices     += CApp::$performance["notice"];

CAccessLog::bufferize(array($log));

if (!CAppUI::conf("log_datasource_metrics")) {
  return;
}

$dslogs = array();
foreach (CSQLDataSource::$dataSources as $_datasource) {
  if ($_datasource) {
    $dslog                   = new CDataSourceLog();
    $dslog->module_action_id = $log->module_action_id;
    $dslog->datasource       = $_datasource->dsn;
    $dslog->requests         = $_datasource->chrono->nbSteps;
    $dslog->duration         = round(floatval($_datasource->chrono->total), 3);
    $dslog->period           = $log->period;
    $dslog->aggregate        = $log->aggregate;
    $dslog->bot              = $log->bot;
    $dslogs[] = $dslog;
  }
}

CDataSourceLog::bufferize($dslogs);


