<?php /* $Id: view_history.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

//require_once($AppUI->getSystemClass("accesslog"));
require_once($AppUI->getModuleClass("system", "accesslog"));

$date     = mbGetValueFromGetOrSession("date", mbDate());
$groupmod = mbGetValueFromGetOrSession("groupmod", 1);
$next     = mbDate("+ 1 day", $date);

$logs = new CAccessLog;
$logs = $logs->loadAgregation($date, $next, $groupmod);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;
$smarty->assign("logs", $logs);
$smarty->assign("date", $date);
$smarty->assign("groupmod", $groupmod);
$smarty->display("view_access_logs.tpl");
?>