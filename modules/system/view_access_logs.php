<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$date     = mbGetValueFromGetOrSession("date"    , mbDate());
$groupmod = mbGetValueFromGetOrSession("groupmod", 2);
$interval = mbGetValueFromGetOrSession("interval", "day");

$module = null;
if (!is_numeric($groupmod)) {
  $module = $groupmod;
  $groupmod = 0;
}

CAppUI::requireModuleFile('dPstats', 'graph_accesslog');

$next     = mbDate("+1 DAY", $date);
switch($interval) {
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
  default:
    $from = mbDate("-1 DAY", $next);
}

$logs = new CAccessLog;
$logs = $logs->loadAgregation($from, $next, $groupmod, $module);

$graphs = array();
foreach($logs as $log) {
	switch($groupmod) {
		case 0: $graphs[] = graphAccessLog($log->module, $log->action, $date, $interval); break;
	  case 1: $graphs[] = graphAccessLog($log->module, null, $date, $interval); break;
	  case 2: $graphs[] = graphAccessLog(null, null, $date, $interval); break;
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graphs"     , $graphs);
$smarty->assign("date"       , $date);
$smarty->assign("groupmod"   , $groupmod);
$smarty->assign("module"     , $module);
$smarty->assign("interval"   , $interval);
$smarty->assign("listModules", CModule::getInstalled());

$smarty->display("view_access_logs.tpl");

?>