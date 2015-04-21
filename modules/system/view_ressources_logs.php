<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date     = CValue::getOrSession("date"    , CMbDT::date());
$groupres = CValue::getOrSession("groupres", 1);
$element  = CValue::getOrSession("element" , "duration");
$interval = CValue::getOrSession("interval", "day");
$numelem  = CValue::getOrSession("numelem" , 6);

CView::enableSlave();

CAppUI::requireModuleFile('dPstats', 'graph_ressourceslog');

$next     = CMbDT::date("+1 DAY", $date);
switch ($interval) {
  default:
  case "day":
    $from = CMbDT::date("-1 DAY", $next);
    break;
  case "month":
    $from = CMbDT::date("-1 MONTH", $next);
    break;
  case "year":
    $from = CMbDT::date("-6 MONTH", $next);
    break;
}

$graphs = array();
if ($groupres == 1) {
  if ($element != "_average_duration" && $element != "_average_request" && $element != "_average_nb_requests") {
    $graphs[] = graphRessourceLog('modules', $date, $element, $interval, $numelem);
  }
  $graphs[] = graphRessourceLog('total', $date, $element, $interval, $numelem);
}
else {
  $logs = CAccessLog::loadAggregation($from, $next, ($groupres + 1), 0);
  foreach ($logs as $log) {
    $graphs[] = graphRessourceLog($log->_module, $date, $element, $interval, $numelem);
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
