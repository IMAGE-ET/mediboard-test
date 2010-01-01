<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m, $action;

$ds = CSQLDataSource::get("std");
if (!$ds->loadTable("access_log") || !$action) { // $sction is not defined when the module is inactive
  return;
}

$period = mbTransformTime(null, null, "%Y-%m-%d %H:00:00");

$log = new CAccessLog();
$log->module   = $m;
$log->action   = $action;
$log->period   = $period;

if (!$log->loadMatchingObject()) {
  $log->hits     = 0;
  $log->duration = 0.0;
  $log->request  = 0.0;
  $log->size     = 0;
  $log->errors   = 0;
  $log->warnings = 0;
  $log->notices  = 0;
}

$log->hits++;
$log->duration += $phpChrono->total;
$log->request  += $ds->chrono->total;
$log->size     += ob_get_length();
$log->errors   += $performance["error"];
$log->warnings += $performance["warning"];
$log->notices  += $performance["notice"];

if ($msg = $log->store()) {
  trigger_error($msg, E_USER_WARNING);
}
