<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;

$dialog = CValue::get("dialog");
$start  = CValue::get("start", 0);

if (!$can->read && !$dialog) {
  $can->redirect();
}

$filter = new CUserLog();
$filter->_date_min    = CValue::getOrSession("_date_min");
$filter->_date_max    = CValue::getOrSession("_date_max");
$filter->user_id      = CValue::getOrSession("user_id");
$filter->object_id    = CValue::getOrSession("object_id");
$filter->object_class = CValue::getOrSession("object_class");
$filter->type         = CValue::getOrSession("type");

// Récupération de la liste des classes disponibles
CAppui::getAllClasses();
$listClasses = getChildClasses();

// Récupération de la liste des utilisateurs disponibles
$user = new CUser;
$user->template = "0";
$order = "user_last_name, user_first_name";
$listUsers = $user->loadMatchingList($order);

// Récupération des logs correspondants
$where = array();
if ($filter->user_id     ) $where["user_id"     ] = "= '$filter->user_id'";
if ($filter->object_id   ) $where["object_id"   ] = "= '$filter->object_id'";
if ($filter->object_class) $where["object_class"] = "= '$filter->object_class'";
if ($filter->type        ) $where["type"        ] = "= '$filter->type'";
if ($filter->_date_min   ) $where[] = "date >= '$filter->_date_min'";
if ($filter->_date_max   ) $where[] = "date <= '$filter->_date_max'";

$log = new CUserLog;
$list = $log->loadList($where, "date DESC", "$start,100");
$list_count = $log->countList($where);

$item = "";
foreach($list as $key => $log) {
  $log->loadRefsFwd();
  $log->getOldValues();
  if (!$item)
    $item = $log->_ref_object->_view;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dialog"      , $dialog      );
$smarty->assign("filter"      , $filter      );
$smarty->assign("listClasses" , $listClasses );
$smarty->assign("listUsers"   , $listUsers   );
$smarty->assign("item"        , $item        );
$smarty->assign("list"        , $list        );
$smarty->assign("start"       , $start       );
$smarty->assign("list_count"  , $list_count  );

$smarty->display("view_history.tpl");

?>