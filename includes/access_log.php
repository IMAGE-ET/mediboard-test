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
if (!$ds->loadTable("access_log")) {
  return;
}

$module = $m;
$period = mbTransformTime(null, null, "%Y-%m-%d %H:00:00");

$where = array(
  "module" => "= '$module'",
  "action" => "= '$action'",
  "period" => "= '$period'",
);

$log = new CAccessLog();
$log->loadObject($where);
if (!$log->accesslog_id) {
  $log->module   = $module;
  $log->action   = $action;
  $log->period   = $period;
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
?>
