<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$date     = mbGetValueFromGetOrSession("date"    , mbDate());
$groupres = mbGetValueFromGetOrSession("groupres", 1);
$element  = mbGetValueFromGetOrSession("element" , "duration");
$interval = mbGetValueFromGetOrSession("interval", "day");
$numelem  = mbGetValueFromGetOrSession("numelem" , 4);

CAppUI::requireModuleFile('dPstats', 'graph_ressourceslog');

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
  default:
    $from = mbDate("-1 DAY", $next);
}


$graphs = array();
if ($groupres == 1) {
  $graphs[] = graphRessourceLog('modules', $date, $element, $interval, $numelem);
  $graphs[] = graphRessourceLog('total', $date, $element, $interval, $numelem);
}
else {
	$logs = new CAccessLog;
  $logs = $logs->loadAgregation($from, $next, ($groupres + 1), 0);
	
  foreach($logs as $log){
    $graphs[] = graphRessourceLog($log->module, $date, $element, $interval, $numelem);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graphs"     , $graphs);
$smarty->assign("date"       , $date);
$smarty->assign("groupres"   , $groupres);
$smarty->assign("element"    , $element);
$smarty->assign("interval"   , $interval);
$smarty->assign("numelem"    , $numelem);
$smarty->assign("listModules", CModule::getInstalled());

$smarty->display("view_ressources_logs.tpl");

?>