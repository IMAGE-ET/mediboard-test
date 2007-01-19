<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

$date     = mbGetValueFromGetOrSession("date"    , mbDate());
$groupres = mbGetValueFromGetOrSession("groupres", 1);
$element  = mbGetValueFromGetOrSession("element" , "duration");
$interval = mbGetValueFromGetOrSession("interval", "day");
$numelem  = mbGetValueFromGetOrSession("numelem" , 4);
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

$logs = new CAccessLog;
$logs = $logs->loadAgregation($from, $next, ($groupres + 1), 0);

$listModules = CModule::getInstalled();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("logs"       , $logs);
$smarty->assign("date"       , $date);
$smarty->assign("groupres"   , $groupres);
$smarty->assign("element"    , $element);
$smarty->assign("interval"   , $interval);
$smarty->assign("numelem"    , $numelem);
$smarty->assign("listModules", $listModules);

$smarty->display("view_ressources_logs.tpl");

?>