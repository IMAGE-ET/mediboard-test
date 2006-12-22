<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

$dialog = mbGetValueFromGet("dialog");

if (!$canRead && !$dialog) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$user_id      = mbGetValueFromGetOrSession("user_id"     , null);
$object_id    = mbGetValueFromGetOrSession("object_id"   , null);
$object_class = mbGetValueFromGetOrSession("object_class", null);
$type         = mbGetValueFromGetOrSession("type"        , null);

// Récupération de la liste des classes disponibles
$AppUI->getAllClasses();
$listClasses = getChildClasses();

// Récupération de la liste des utilisateurs disponibles
$where = array();
$where["user_username"] = "NOT LIKE '>>%'";
$order = "user_last_name, user_first_name";
$listUsers = new CUser;
$listUsers = $listUsers->loadList($where, $order);

// Récupération des types disponibles
$userLog = new CUserLog;
$userLog->buildEnums();

// Récupération des logs correspondants
$where = array();
if($user_id)
  $where["user_id"] = "= '$user_id'";
if($object_id !== "" && $object_id !== null)
  $where["object_id"] = "= '$object_id'";
if($object_class)
  $where["object_class"] = "= '$object_class'";
if($type)
  $where["type"] = "= '$type'";
$order = "date DESC";
$list = new CUserLog;
$list = $list->loadList($where, $order, "0, 100");
$item = "";
foreach($list as $key => $value) {
  $list[$key]->loadRefsFwd();
  if($item == "")
    $item = $list[$key]->_ref_object->_view;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("dialog"      , $dialog      );
$smarty->assign("object_class", $object_class);
$smarty->assign("object_id"   , $object_id   );
$smarty->assign("user_id"     , $user_id     );
$smarty->assign("type"        , $type        );
$smarty->assign("listClasses" , $listClasses );
$smarty->assign("listUsers"   , $listUsers   );
$smarty->assign("userLog"     , $userLog     );
$smarty->assign("item"        , $item        );
$smarty->assign("list"        , $list        );

$smarty->display("view_history.tpl");

?>