<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Alexis Granger
*/

global $can;

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Récupération des logs correspondants
$logs = array();

$log = new CUserLog;
$log->setObject($object);
$order = "date DESC";
$logs = $log->loadMatchingList($order);

foreach($logs as $key => $_log) {
  $_log->setObject($object);
  $_log->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("logs", $logs);

$smarty->display("vw_object_history.tpl");
?>