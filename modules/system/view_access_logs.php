<?php /* $Id: view_history.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

$date     = mbGetValueFromGetOrSession("date"    , mbDate());
$groupmod = mbGetValueFromGetOrSession("groupmod", 2);
$module   = mbGetValueFromGetOrSession("module"  , "system");
$interval = mbGetValueFromGetOrSession("interval", "day");
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
$logs = $logs->loadAgregation($from, $next, $groupmod, $module);

$listModules = CModule::getInstalled();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("logs"       , $logs);
$smarty->assign("date"       , $date);
$smarty->assign("groupmod"   , $groupmod);
$smarty->assign("module"     , $module);
$smarty->assign("interval"   , $interval);
$smarty->assign("listModules", $listModules);

$smarty->display("view_access_logs.tpl");

?>