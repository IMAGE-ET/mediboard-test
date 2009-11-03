<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$date     = CValue::getOrSession("date"    , mbDate());
$groupmod = CValue::getOrSession("groupmod", 2);
$interval = CValue::getOrSession("interval", "day");

$left_mode      = CValue::getOrSession("left_mode", "request_time"); // request_time, errors
$left_sampling  = CValue::getOrSession("left_sampling", "total"); // total, mean

$right_mode     = CValue::getOrSession("right_mode", "hits"); // hits, size
$right_sampling = CValue::getOrSession("right_sampling", "total"); // total, mean

$module = null;
if (!is_numeric($groupmod)) {
  $module = $groupmod;
  $groupmod = 0;
}

CAppUI::requireModuleFile('dPstats', 'graph_accesslog');

$next     = mbDate("+1 DAY", $date);
switch($interval) {
  default:
  case "day":
    $from = mbDate("-1 DAY", $next);
    break;
  case "month":
    $from = mbDate("-1 MONTH", $next);
    break;
  case "hyear":
    $from = mbDate("-6 MONTH", $next);
    break;
  case "twoyears":
    $from = mbDate("-2 YEARS", $next);
    break;
  case "twentyyears":
    $from = mbDate("-20 YEARS", $next);
    break;
}

$logs = new CAccessLog;
$logs = $logs->loadAgregation($from, $next, $groupmod, $module);

$graphs = array();
$left = array($left_mode, $left_sampling);
$right = array($right_mode, $right_sampling);
foreach($logs as $log) {
	switch($groupmod) {
		case 0: $graphs[] = graphAccessLog($log->module, $log->action, $date, $interval, $left, $right); break;
	  case 1: $graphs[] = graphAccessLog($log->module, null, $date, $interval, $left, $right); break;
	  case 2: $graphs[] = graphAccessLog(null, null, $date, $interval, $left, $right); break;
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graphs"     , $graphs);
$smarty->assign("date"       , $date);
$smarty->assign("groupmod"   , $groupmod);

$smarty->assign("left_mode"    , $left_mode);
$smarty->assign("left_sampling", $left_sampling);

$smarty->assign("right_mode"    , $right_mode);
$smarty->assign("right_sampling", $right_sampling);

$smarty->assign("module"     , $module);
$smarty->assign("interval"   , $interval);
$smarty->assign("listModules", CModule::getInstalled());

$smarty->display("view_access_logs.tpl");

?>