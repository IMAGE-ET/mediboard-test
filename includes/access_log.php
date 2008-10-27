<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author Thomas Despoix
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
  $log->module = $module;
  $log->action = $action;
  $log->period = $period;
  $log->hits = 0;
  $log->duration = 0.0;
  $log->request = 0.0;
}

$log->hits++;
$log->duration += $phpChrono->total;
$log->request += $ds->chrono->total;

$log->store();
?>
