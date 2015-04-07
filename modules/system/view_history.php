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

CCanDo::check();

$dialog = CValue::get("dialog");
$start  = CValue::get("start", 0);
$stats  = CValue::get("stats", 0);
$period = CValue::get("period", "day");
$csv = CValue::get("csv", 0);

if (!CCanDo::read() && !$dialog) {
  global $can;
  $can->redirect();
}

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

CView::enforceSlave();

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

switch ($object->_class) {
  case "CCompteRendu":
    // Inclusion des logs sur l'objet CContentHTML
    $object->loadContent(false);
    $content = $object->_ref_content;
    // To activate force index below
    $where["object_id"] = "IN ('$filter->object_id', '$content->_id')";
    // Actual query
    $where[] = "
    (object_id = '$filter->object_id' AND object_class = '$filter->object_class') OR
    (object_id = '$content->_id'      AND object_class = 'CContentHTML')";
    break;
  default:
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

$is_admin = CCanDo::admin();

$dossiers_medicaux_shared = CAppUI::conf("dPetablissement dossiers_medicaux_shared");

if (!$stats) {
  $index = isset($where["object_id"]) ? "object_id" : null;
  /** @var CUserLog[] $list */
  $list       = $log->loadList($where, "date DESC, user_log_id DESC", "$start, 100", null, null, $index);
  $list_count = $log->countList($where, null, null, $index);

  $group_id = CGroups::loadCurrent()->_id;
  $users = CStoredObject::massLoadFwdRef($list, "user_id");
  CStoredObject::massLoadFwdRef($list, "object_id");

  // Mass loading des mediusers et des fonctions
  $mediuser = new CMediusers();
  $mediusers = $mediuser->loadList(array("user_id" => CSQLDataSource::prepareIn(array_keys($users))));
  CStoredObject::massLoadFwdRef($mediusers, "function_id");

  foreach ($list as $_log) {
    $_log->loadRefUser();

    $function = isset($mediusers[$_log->user_id]) ?
      $mediusers[$_log->user_id]->loadRefFunction() :
      $_log->_ref_user->loadRefMediuser()->loadRefFunction();

    if (!$is_admin && !$dossiers_medicaux_shared && $function->group_id != $group_id) {
      unset($list[$_log->_id]);
      continue;
    }

    $target = $_log->loadTargetObject();
    $_log->getOldValues();
  }
}

if ($csv) {
  ob_clean();
  $date = CMbDT::dateTime();
  header("Content-type: text/csv");
  header('Content-Type: text/html;charset=ISO-8859-1');
  header("Content-disposition: attachment; filename='journal_utilisateur_$date-$filter->type.csv'");
  echo "\"Classe\";\"Id\";\"IP\";\"Utilisateur\";\"Date\";\"Type\";\"Champs\";\n";
  foreach ($list as $_log) {
    $ref_object = $_log->_ref_object;
    echo "\"".CAppUI::tr($_log->object_class)."\";";
    echo "\"";
    if ($_log->_ref_object->_id) {
      echo $_log->_ref_object;
    }
    else {
      echo $_log->_ref_object;
      if ($_log->extra) {
        echo ' - ';
      }
    }
    echo "\";";
    echo "\"";
    echo $_log->ip_address ? inet_ntop($_log->ip_address) : null;
    echo "\";";
    echo "\"".$_log->_ref_user->_view."\";";
    echo "\"".$_log->date."\";";
    echo "\"".CAppUI::tr($_log->type)."\";";
    echo "\"";
    if ($object->_id) {
      foreach ($_log->_fields as $_field) {
        if (array_key_exists($_field, $object->_specs)) {
          echo CAppUI::tr($object->$_field);
        }
        else {
          echo CAppUI::tr('CMbObject.missing_spec');
        }

        if (array_key_exists($_field,$_log->_old_values)) {
          echo $object->$_field;
        }
      }
    }
    else {
      if (strpos($_log->object_class, "CExObject_") === false && is_array($_log->_fields)) {
        foreach ($_log->_fields as $_field) {
          if (array_key_exists($_field, $ref_object->_specs)) {
            echo CAppUI::tr($_log->object_class."-".$_field);
            echo " - ";
          }
          else {
            echo CAppUI::tr('CMbObject.missing_spec')." ($_field)";
          }
        }
      }
    }
    echo "\";";
    echo "\n";
  }
  CApp::rip();
}

$smarty = new CSmartyDP();

$smarty->assign("dialog",      $dialog      );
$smarty->assign("filter",      $filter      );
$smarty->assign("object",      $object      );
$smarty->assign("listClasses", $listClasses );
$smarty->assign("list",        $list        );
$smarty->assign("start",       $start       );
$smarty->assign("list_count",  $list_count  );
$smarty->assign("stats",       $stats       );
$smarty->assign("csv",         $csv         );
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