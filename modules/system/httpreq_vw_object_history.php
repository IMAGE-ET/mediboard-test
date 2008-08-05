<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $ajax;

$object_class = mbGetValueFromGet("object_class");
$object_id    = mbGetValueFromGet("object_id");

if (!$object_class || !$object_id) {
  return;
}

$object = new $object_class;
$object->load($object_id);
if (!$object->_id) {
  $AppUI->redirect("?ajax=$ajax&suppressHeaders=1&m=$m&a=object_not_found&object_classname=$object_class");
}

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