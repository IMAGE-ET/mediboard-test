<?php 
/**
 * Access logging 
 *
 * PHP version 5.1.x+
 *  
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

if (CAppUI::conf("readonly")) {
	return;
}

global $m, $action;

// Check prerequisites
$ds = CSQLDataSource::get("std");
// $action may not defined when the module is inactive
if (!$ds->loadTable("access_log") || !$action) { 
  return;
}

// Key initialisation
$log = new CAccessLog();
$log->module   = $m;
$log->action   = $action;
$log->period   = mbTransformTime(null, null, "%Y-%m-%d %H:00:00");;

// Probe aquisition
$rusage = getrusage();
$log->hits++;
$log->duration    += $phpChrono->total;
$log->processus   += floatval($rusage["ru_utime.tv_usec"]) / 1000000 + $rusage["ru_utime.tv_sec"];
$log->processor   += floatval($rusage["ru_stime.tv_usec"]) / 1000000 + $rusage["ru_stime.tv_sec"];
$log->request     += $ds->chrono->total;
$log->size        += ob_get_length();
$log->peak_memory += memory_get_peak_usage();
$log->errors      += $performance["error"];
$log->warnings    += $performance["warning"];
$log->notices     += $performance["notice"];

// Fast store
if ($msg = $log->fastStore()) {
  trigger_error($msg, E_USER_WARNING);
}
