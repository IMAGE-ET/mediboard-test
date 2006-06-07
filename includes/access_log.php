<?php /* $Id: errors.php 26 2006-05-04 16:12:16Z Rhum1 $ */

/**
 * @package Mediboard
 * @subpackage Includes
 * @version $Revision: 26 $
 * @author Thomas Despoix
 */

global $AppUI;

//require_once($AppUI->getSystemClass("accesslog"));
require_once($AppUI->getModuleClass("system", "accesslog"));

$module = $m;
$action = mbGetValue(@$tab, $a, $dosql);
$period = mbTranformTime(null, null, "%Y-%m-%d %H:00:00");

$where = array();
$where["module"] = "= '$module'";
$where["action"] = "= '$action'";
$where["period"] = "= '$period'";

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
$log->request += $dbChronos["std"]->total;
$log->store();
?>
