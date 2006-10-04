<?php /* $Id: view_history.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

$date     = mbGetValueFromGetOrSession("date", mbDate());
$groupmod = mbGetValueFromGetOrSession("groupmod", 1);
$module   = mbGetValueFromGetOrSession("module", "system");
$next     = mbDate("+ 1 day", $date);

$logs = new CAccessLog;
$logs = $logs->loadAgregation($date, $next, $groupmod, $module);

$listModules = CModule::getInstalled();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("logs"       , $logs);
$smarty->assign("date"       , $date);
$smarty->assign("groupmod"   , $groupmod);
$smarty->assign("module"     , $module);
$smarty->assign("listModules", $listModules);

$smarty->display("view_access_logs.tpl");

?>