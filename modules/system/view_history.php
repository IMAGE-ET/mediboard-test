<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dialog = CValue::get("dialog");
$start  = CValue::get("start", 0);
$stats  = CValue::get("stats", 0);
$period = CValue::get("period", "day");

if (!CCanDo::edit() && !$dialog) {
  global $can;
  $can->redirect();
}

$smarty = new CSmartyDP();

$filter = new CUserLog();

if (!$dialog) {
  $filter->_date_min    = CValue::getOrSession("_date_min");
  $filter->_date_max    = CValue::getOrSession("_date_max");
  $filter->user_id      = CValue::getOrSession("user_id");
  $filter->object_id    = CValue::getOrSession("object_id");
  $filter->object_class = CValue::getOrSession("object_class");
  $filter->type         = CValue::getOrSession("type");
}
else {
  $filter->_date_min    = CValue::get("_date_min");
  $filter->_date_max    = CValue::get("_date_max");
  $filter->user_id      = CValue::get("user_id");
  $filter->object_id    = CValue::get("object_id");
  $filter->object_class = CValue::get("object_class");
  $filter->type         = CValue::get("type");
}

$ex_class_id          = CValue::get("ex_class_id");

// Limit to a default one month for no context queries
if (!$filter->_date_min && !$filter->object_id && !$filter->user_id && !$ex_class_id) {
  $filter->_date_min = CMbDT::dateTime("-1 month");
}

$object = new CMbObject();
if ($filter->object_id && $filter->object_class) {
  /** @var CStoredObject $object */
  $object = new $filter->object_class;

  if ($ex_class_id && $filter->object_class == "CExObject") {
    /** @var CExObject $object */
    $object->_ex_class_id = $ex_class_id;
    $object->setExClass();
    $filter->object_class .= "_$ex_class_id";
  }

  $object->load($filter->object_id);
  $object->loadHistory();
}

// Récupération de la liste des classes disponibles
$listClasses = array();

if (!$dialog) {
  $listClasses = CApp::getChildClasses();
}

$filter->loadRefUser();

// Récupération des logs correspondants
$where = array();
if ($filter->user_id) {
  $where["user_id"] = "= '$filter->user_id'";
}

// Inclusion des logs sur l'objet CContentHTML si c'est un compte-rendu
if ($object instanceof CCompteRendu) {
  $object->loadContent(false);
  $content = $object->_ref_content;
  $where[] = "(object_id = '$filter->object_id' AND object_class = '$filter->object_class') OR
(object_id = '$content->_id' AND object_class = 'CContentHTML')";
}
else {
  if ($filter->object_id) {
    $where["object_id"   ] = "= '$filter->object_id'";
  }
  if ($filter->object_class) {
    $where["object_class"] = "= '$filter->object_class'";
  }
}

if ($filter->type) {
  $where["type"] = "= '$filter->type'";
}
if ($filter->_date_min) {
  $where[] = "date >= '$filter->_date_min'";
}
if ($filter->_date_max) {
  $where[] = "date <= '$filter->_date_max'";
}

$log = new CUserLog();

$list       = null;
$list_count = null;

$is_admin = CCanDo::checkAdmin();

if (!$stats) {
  $list       = $log->loadList($where, "user_log_id DESC", "$start, 100");
  $list_count = $log->countList($where);

  $group_id = CGroups::loadCurrent()->_id;
  CMbObject::massLoadFwdRef($list, "user_id");
  CMbObject::massLoadFwdRef($list, "object_id");

  foreach ($list as $key => $log) {
    $log->loadRefUser();
    $log->_ref_user->loadRefMediuser();

    $mediuser = $log->_ref_user->_ref_mediuser;
    $mediuser->loadRefFunction();

    if (!$is_admin && $mediuser->_ref_function->group_id != $group_id) {
      unset($list[$key]);
      continue;
    }

    $log->loadTargetObject();
    $log->getOldValues();
  }
}

$smarty->assign("dialog",      $dialog      );
$smarty->assign("filter",      $filter      );
$smarty->assign("object",      $object      );
$smarty->assign("listClasses", $listClasses );
$smarty->assign("list",        $list        );
$smarty->assign("start",       $start       );
$smarty->assign("list_count",  $list_count  );
$smarty->assign("stats",       $stats       );
$smarty->assign("period",      $period      );

if ($stats) {
  CAppUI::requireModuleFile('dPstats', 'graph_userlog');

  if (!$filter->_date_max) {
    $filter->_date_max = CMbDT::dateTime();
  }

  $graphs[] =
    graphUserLogSystem(
      $filter->_date_min,
      $filter->_date_max,
      $period,
      $filter->type,
      $filter->user_id,
      $filter->object_class,
      $filter->object_id
    );

  $smarty->assign("graphs", $graphs);
}

$smarty->display("view_history.tpl");