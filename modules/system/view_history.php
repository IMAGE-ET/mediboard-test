<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("system", "user_log"));

// Require all dPmodules class
foreach(glob("modules/dP*/*.class.php") as $fileName) {
  require_once($AppUI->getConfig("root_dir")."/".$fileName);
}
// Add the user class
require_once($AppUI->getModuleClass("admin"));

$dialog = mbGetValueFromGet("dialog", 0);

if (!$canRead && !$dialog) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$user_id      = mbGetValueFromGetOrSession("user_id"     , null);
$object_id    = mbGetValueFromGetOrSession("object_id"   , null);
$object_class = mbGetValueFromGetOrSession("object_class", null);
$type         = mbGetValueFromGetOrSession("type"        , null);

// Récupération de la liste des classes disponibles
$where       = array();
$where[]     = "1";
$order       = "object_class";
$group       = "object_class";
$list        = new CUserLog;
$list        = $list->loadList($where, $order, null, $group);
$listClasses = array();

foreach($list as $key => $value) {
  $listClasses[] = $value->object_class;
}

// Récupération de la liste des utilisateurs disponibles
$where = array();
$where["user_username"] = "NOT LIKE '>>%'";
$order = "user_last_name, user_first_name";
$listUsers = new CUser;
$listUsers = $listUsers->loadList($where, $order);

// Récupération des types disponibles
$where = array();
$where[] = "1";
$order = "type";
$group = "type";
$list = new CUserLog;
$list = $list->loadList($where, $order, null, $group);
$listTypes = array();
foreach($list as $key => $value) {
  $listTypes[] = $value->type;
}

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
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("dialog"      , $dialog      );
$smarty->assign("object_class", $object_class);
$smarty->assign("object_id"   , $object_id   );
$smarty->assign("user_id"     , $user_id     );
$smarty->assign("type"        , $type        );
$smarty->assign("listClasses" , $listClasses );
$smarty->assign("listUsers"   , $listUsers   );
$smarty->assign("listTypes"   , $listTypes   );
$smarty->assign("item"        , $item        );
$smarty->assign("list"        , $list        );

$smarty->display("view_history.tpl");

?>