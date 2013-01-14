<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$reference_class = CValue::get("reference_class");
$reference_id    = CValue::get("reference_id");
$detail          = CValue::get("detail", 1);
$ex_class_id     = CValue::get("ex_class_id");
$target_element  = CValue::get("target_element");
$print           = CValue::get("print");
$start           = CValue::get("start", 0);

// Search mode
$search_mode     = CValue::get("search_mode", 0);
$date_min        = CValue::get("date_min");
$date_max        = CValue::get("date_max");
$group_id        = CValue::get("group_id");
$concept_search  = CValue::get("concept_search");

CValue::setSession('reference_class', $reference_class);
CValue::setSession('reference_id',    $reference_id);

if ($reference_class) {
  $reference = new $reference_class;

  if ($reference_id) {
    $reference->load($reference_id);
  }
}
else {
  $reference = null;
}

CExClassField::$_load_lite = true;
CExObject::$_multiple_load = true;
CExObject::$_load_lite = $detail < 2;

$group_id = ($group_id ? $group_id : CGroups::loadCurrent()->_id);
$where = array(
  "group_id = $group_id OR group_id IS NULL",
);

if ($ex_class_id) {
  $where['ex_class_id'] = "= '$ex_class_id'";
}

if (empty(CExClass::$_list_cache)) {
  $ex_class = new CExClass;
  CExClass::$_list_cache = $ex_class->loadList($where, "name");

  if (!CExObject::$_locales_cache_enabled && $detail > 1) {
    foreach (CExClass::$_list_cache as $_ex_class) {
      foreach ($_ex_class->loadRefsGroups() as $_group) {
        $_group->loadRefsFields();
        foreach ($_group->_ref_fields as $_field) {
          $_field->updateTranslation();
        }
      }
    }
  }
}

$all_ex_objects = array();
$ex_objects = array();
$ex_classes = array();
$ex_objects_counts = array();
$ex_classes_creation = array();

$limit = null;

if ($print) {
  $limit = 5;
}
else {
  switch ($detail) {
    case 3:
    case 2:
      $limit = ($search_mode ? 50 : ($ex_class_id ? 20 : 10));
      break;
    case 1:
      $limit = ($ex_class_id ? 50 : 25);
      break;
    default:
    case 0:
  }
}

$step = $limit;
$total = 0;

if ($limit) {
  $limit = "$start, $limit";
}

$ref_objects_cache = array();

$search = null;
if ($concept_search) {
  $concept_search = stripslashes($concept_search);
  $search = CExConcept::parseSearch($concept_search);
}

$ex_class_event = new CExClassEvent;

foreach (CExClass::$_list_cache as $_ex_class_id => $_ex_class) {
  $_ex_object = new CExObject;
  $_ex_object->_ex_class_id = $_ex_class_id;
  $_ex_object->setExClass();

  if ($search_mode) {
    $where = array(
      "group_id" => "= '$group_id'",
      "user_log.date" => "BETWEEN '$date_min' AND '$date_max'",
      "user_log.type" => "= 'create'",
    );

    $ljoin = array(
      "user_log" => "user_log.object_id = {$_ex_object->_spec->table}.ex_object_id AND user_log.object_class = '$_ex_object->_class'"
    );

    if (!empty($search)) {
      $where = array_merge($where, $_ex_class->getWhereConceptSearch($search));
    }
  }
  else {
    $where = array(
      "(reference_class  = '$reference_class' AND reference_id  = '$reference_id') OR
       (reference2_class = '$reference_class' AND reference2_id = '$reference_id') OR
       (object_class     = '$reference_class' AND object_id     = '$reference_id')"
    );

    $ljoin = array();
  }

  $_ex_objects = array();

  if ($detail >= 1) {
    $_ex_objects = $_ex_object->loadList($where, "{$_ex_object->_spec->key} DESC", $limit, null, $ljoin);
  }

  $_ex_objects_count = $_ex_object->countList($where, null, $ljoin);

  $total = max($_ex_objects_count, $total);

  if ($_ex_objects_count) {
    $ex_objects_counts[$_ex_class_id] = $_ex_objects_count;
  }

  if ($detail <= 0.5 && !$_ex_class->conditional) {
    $where = array(
      "ex_class.ex_class_id"      => "= '$_ex_class_id'",
      "ex_class_event.host_class" => "= '$reference_class'",
      "ex_class_event.disabled"   => "= '0'",
    );
    $ljoin = array(
      "ex_class" => "ex_class.ex_class_id = ex_class_event.ex_class_id",
    );

    $_ex_class_events = $ex_class_event->loadList($where, null, null, null, $ljoin);

    // TODO canCreateNew
    foreach ($_ex_class_events as $_id => $_ex_class_event) {
      if ($reference && (!$_ex_class_event->checkConstraints($reference)/* || !$_ex_class_event->canCreateNew($reference)*/)) {
        unset($_ex_class_events[$_id]);
      }
    }

    if (count($_ex_class_events)) {
      $ex_classes_creation[$_ex_class_id] = $_ex_class_events;
    }
  }

  if ($detail == 0) {
    continue;
  }

  /*
  if ( $_ex_class->host_class == $reference_class && // Possible context
      !$_ex_class->disabled && // Not disabled
       $_ex_class->checkConstraints($reference) && // Passes constraints
       $_ex_class->canCreateNew($reference) // Check unicity
  ) {
    if ($detail > 0 || !$_ex_class->conditional) {
      $ex_classes_creation[$ex_class_key][$_ex_class_id] = $_ex_class;
    }

    if (count($_ex_objects) == 0){
      $ex_objects[$_ex_class_id] = array();
    }
  }*/

  foreach ($_ex_objects as $_ex) {
    $_ex->_ex_class_id = $_ex_class_id;
    $_ex->load();

    $guid = "$_ex->object_class-$_ex->object_id";

    if (!isset($ref_objects_cache[$guid])) {
      $_ex->loadTargetObject()->loadComplete(); // to get the view
      $ref_objects_cache[$guid] = $_ex->_ref_object;
    }
    else {
      $_ex->_ref_object = $ref_objects_cache[$guid];
    }

    $_ex->loadLogs();
    $_log = $_ex->_ref_first_log;

    // Cas tres etrange de formulaire sans aucun log
    // Plutot que de tout planter, on ne l'affiche pas
    if (!$_log) {
      continue;
    }

    $all_ex_objects["$_log->date $_ex->_id"] = $_ex;
    $ex_objects[$_ex_class_id]["$_log->date $_ex->_id"] = $_ex;
  }

  if (isset($ex_objects[$_ex_class_id])) {
    krsort($ex_objects[$_ex_class_id]);
  }
}

if ($detail == 2) {
  foreach ($ex_objects as $_ex_class_id => $_ex_objects) {
    $first = reset($_ex_objects);

    if (!$first) {
      continue;
    }

    $_ex_class = $first->_ref_ex_class;

    foreach ($_ex_class->_ref_groups as $_ex_group) {
      $_ex_group->_empty = true;

      foreach ($_ex_group->_ref_fields as $_ex_field) {
        $_ex_field->_empty = true;

        foreach ($_ex_objects as $_ex_object) {
          if ($_ex_object->{$_ex_field->name} != "") {
            $_ex_field->_empty = false;
            $_ex_group->_empty = false;
            break;
          }
        }
      }
    }
  }
}

ksort($ex_objects);
ksort($all_ex_objects);

// Création du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("reference_class", $reference_class);
$smarty->assign("reference_id",    $reference_id);
$smarty->assign("reference",       $reference);
$smarty->assign("all_ex_objects",  $all_ex_objects);
$smarty->assign("ex_objects",      $ex_objects);
$smarty->assign("ex_objects_counts", $ex_objects_counts);
$smarty->assign("limit",           $limit);
$smarty->assign("step",            $step);
$smarty->assign("total",           $total);
$smarty->assign("ex_classes_creation", $ex_classes_creation);
$smarty->assign("ex_classes",      CExClass::$_list_cache);
$smarty->assign("detail",          $detail);
$smarty->assign("ex_class_id",     $ex_class_id);
$smarty->assign("target_element",  $target_element);
$smarty->assign("print",           $print);
$smarty->assign("start",           $start);
$smarty->assign("search_mode",     $search_mode);
$smarty->display("inc_list_ex_object.tpl");
