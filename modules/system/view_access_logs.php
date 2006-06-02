<?php /* $Id: view_history.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("accesslog"));

$date = mbGetValueFromGet("date", mbDate());
$next = mbDate("+ 1 day", $date);

$logs = new CAccessLog;
$where = array();
$where["period"] = "BETWEEN '$date' AND '$next'";

$logs = $logs->loadList($where);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;
$smarty->assign("logs", $logs);
$smarty->assign("date", $date);
$smarty->display("view_access_logs.tpl");
?>