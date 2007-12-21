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

// Récupération de la liste des utilisateurs disponibles
$user = new CUser;
$user->template = "0";
$order = "user_last_name, user_first_name";
$listUsers = $user->loadMatchingList($order);

// Récupération des types disponibles
$userLog = new CUserLog;
$userLog->buildEnums();

// Récupération des logs correspondants
$logs = array();

$log = new CUserLog;
$log->object_id = $object->_id;
$log->object_class = $object->_class_name;
$order = "date DESC";
$logs = $log->loadMatchingList($order);

foreach($logs as $key => $log) {
  $log->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("logs", $logs);

$smarty->display("vw_object_history.tpl");
?>