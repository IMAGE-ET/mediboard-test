<?php /* $Id: errors.php 26 2006-05-04 16:12:16Z Rhum1 $ */

/**
 * @package Mediboard
 * @subpackage Includes
 * @version $Revision: 26 $
 * @author Thomas Despoix
 */

global $AppUI, $m, $tab, $a, $dosql, $action;


if (!isset($dPconfig['bd'])) {
  if (!db_loadTable("access_log")) {
    return;
  }
}


if (isset($dPconfig['bd']) && ($dPconfig['bd'] == 'sqldataSource')){
  $mysql_data_source = CSQLDataSource::get("std");
  if (!$mysql_data_source->db_loadTable("access_log")) {
    return;
  }
}




$module = $m;
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


if (!isset($dPconfig['bd'])) {
  $log->request += $dbChronos["std"]->total;
}

if (isset($dPconfig['bd']) && ($dPconfig['bd'] == 'sqldataSource')){
  $log->request += $mysql_data_source->chrono->total;
}

$log->store();
?>
