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

$reference_class     = CValue::get("reference_class");
$reference_id        = CValue::get("reference_id");
$cross_context_class = CValue::get("cross_context_class");
$cross_context_id    = CValue::get("cross_context_id");
$creation_context_class = CValue::get("creation_context_class");
$creation_context_id    = CValue::get("creation_context_id");

$detail              = CValue::get("detail", 1);
$ex_class_id         = CValue::get("ex_class_id");
$target_element      = CValue::get("target_element");
$other_container     = CValue::get("other_container");
$print               = CValue::get("print");
$start               = CValue::get("start", 0);
$limit               = CValue::get("limit");
$only_host           = CValue::get("only_host");
$readonly            = CValue::get("readonly");
// Search mode
$search_mode     = CValue::get("search_mode", 0);
$date_min        = CValue::get("date_min");
$date_max        = CValue::get("date_max");
$group_id        = CValue::get("group_id");
$concept_search  = CValue::get("concept_search");

CValue::setSession('reference_class', $reference_class);
CValue::setSession('reference_id',    $reference_id);

if ($reference_class) {
  /** @var CMbObject $reference */
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
  "group_id = '$group_id' OR group_id IS NULL",
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

/** @var CExObject[] $ex_objects */
$ex_objects          = array();

$ex_classes          = array();
$ex_objects_counts   = array();
$ex_objects_results  = array();
$ex_classes_creation = array();

if (!$limit) {
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
}

$step  = $limit;
$total = 0;

if ($limit) {
  $limit = "$start, $limit";
}

$ref_objects_cache = array();

$search = null;
if ($concept_search) {
  $concept_search = stripslashes($concept_search);
  $search         = CExConcept::parseSearch($concept_search);
}

$ex_class_event = new CExClassEvent();
$ex_class_events = null;

$ex_link = new CExLink();

$group_id = ($group_id ? $group_id : CGroups::loadCurrent()->_id);
$where = array(
  "ex_link.group_id" => "= '$group_id'",
);

if ($ex_class_id) {
  $where['ex_link.ex_class_id'] = "= '$ex_class_id'";
}

$ljoin = array();

$creation_context = null;

if (!$creation_context_class || !$creation_context_id) {
  $creation_context_class = $reference_class;
  $creation_context_id    = $reference_id;
}

if ($creation_context_class) {
  /** @var CSejour|CPatient|CConsultation $creation_context */
  $creation_context = new $creation_context_class;
  $creation_context->load($creation_context_id);
}

if ($search_mode) {
  $use_user_logs = $date_min < CExObject::DATE_LIMIT;

  $where["ex_link.level"]    = "= 'object'";

  if ($use_user_logs) {
    $where["user_log.date"]    = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
    $where["user_log.type"]    = "= 'create'";
  }
  else {
    $where["ex_link.datetime_create"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
  }

  if (!$ex_class_id) {
    if ($use_user_logs) {
      $where["user_log.object_class"] = "LIKE 'CExObject%'";

      $ljoin["user_log"] =
        "user_log.object_id = ex_link.ex_object_id AND user_log.object_class = CONCAT('CExObject_',ex_link.ex_object_id)";
    }
  }
  else {
    if ($use_user_logs) {
      $ljoin["user_log"] = "user_log.object_id = ex_link.ex_object_id AND user_log.object_class = 'CExObject_$ex_class_id'";
    }
    else {
      $where["ex_link.ex_class_id"] = "= '$ex_class_id'";
    }
  }

  if (!empty($search)) {
    $_ex_class = new CExClass();
    $_ex_class->load($ex_class_id);

    $ljoin["ex_object_$ex_class_id"] = "ex_object_$ex_class_id.ex_object_id = ex_link.ex_object_id";

    $where = array_merge($where, $_ex_class->getWhereConceptSearch($search));
  }
}
else {
  if ($cross_context_class && $cross_context_id) {
    $where["ex_link.object_class"] = "= '$cross_context_class'";
    $where["ex_link.object_id"]    = "= '$cross_context_id'";

    $where["ex_class.cross_context_class"] = "= '$cross_context_class'";
  }
  else {
    $where["ex_link.object_class"] = "= '$reference_class'";
    $where["ex_link.object_id"]    = "= '$reference_id'";
  }

  if ($only_host) {
    $where["ex_link.level"] = " = 'object'";
  }
}

$order = "ex_class.name ASC, ex_link.ex_object_id DESC";

$ljoin["ex_class"] = "ex_class.ex_class_id = ex_link.ex_class_id";

$fields = array(
  "ex_link.ex_class_id",
  "ex_link.ex_object_id",
);
$counts = $ex_link->countMultipleList($where, $order, "ex_class_id", $ljoin, $fields);

foreach ($counts as $_count) {
  $_total       = $_count["total"];
  $_ex_class_id = $_count["ex_class_id"];

  $_ex_class = CExClass::$_list_cache[$_ex_class_id];

  // Counts
  $total = max($_total, $total);
  $ex_objects_counts[$_ex_class_id] = $_total;

  // Formula results
  $ex_objects_results[$_ex_class_id] = null;
  if ($_ex_class->_formula_field && !$search_mode) {
    $where_formula = $where;
    unset($where_formula["ex_class.cross_context_class"]);
    $ex_objects_results[$_ex_class_id] = $_ex_class->getFormulaResult($_ex_class->_formula_field, $where_formula);
  }

  if ($detail < 1) {
    continue;
  }

  /** @var CExLink[] $links */
  $where["ex_link.ex_class_id"]  = "= '$_ex_class_id'";
  $links = $ex_link->loadList($where, $order, $limit, "ex_link.ex_object_id", $ljoin);

  CExLink::massLoadExObjects($links);

  /** @var CExObject[] $_ex_objects */
  $_ex_objects = array();
  foreach ($links as $_link) {
    $_ex = $_link->loadRefExObject();
    $_ex->_ex_class_id = $_link->ex_class_id;
    $_ex->load();

    $_ex_objects[$_link->ex_object_id] = $_ex;
  }
  
  /** @var CExObject $_ex */
  foreach ($_ex_objects as $_ex) {
    if (!$_ex->_id) {
      continue;
    }

    $_ex->updateCreationFields();

    $guid = "$_ex->object_class-$_ex->object_id";

    if (!isset($ref_objects_cache[$guid])) {
      $_ex->loadTargetObject();
      
      if ($detail < 2) {
        $_ex->loadComplete(); // to get the view
      }
      
      $ref_objects_cache[$guid] = $_ex->_ref_object;
    }
    else {
      $_ex->_ref_object = $ref_objects_cache[$guid];
    }

    if ($_ex->additional_id) {
      $_ex->loadRefAdditionalObject();
    }

    $ex_objects[$_ex_class_id][$_ex->_id] = $_ex;
  }

  if (isset($ex_objects[$_ex_class_id])) {
    krsort($ex_objects[$_ex_class_id]);
  }
}

// Can create new
if ($detail <= 0.5) {

  // Loading the events
  if ($ex_class_events === null) {
    $_ex_class_creation = array();
    $ex_class_events = array();

    foreach (CExClass::$_list_cache as $_ex_class_id => $_ex_class) {
      if (!$_ex_class->conditional && (!$cross_context_class || $cross_context_class == $_ex_class->cross_context_class)) {
        $_ex_class_creation[] = $_ex_class_id;
      }
    }

    $where = array(
      "ex_class_event.ex_class_id" => $ex_class_event->getDS()->prepareIn($_ex_class_creation),
      "ex_class_event.disabled"    => "= '0'",
    );

    /** @var CExClassEvent[] $ex_class_events_by_ref */
    $ex_class_events_by_ref = $ex_class_event->loadList($where);

    CStoredObject::massLoadBackRefs($ex_class_events_by_ref, "constraints");

    foreach ($ex_class_events_by_ref as $_ex_class_event) {
      $_key = "$_ex_class_event->host_class/$_ex_class_event->ex_class_id";

      /** @var CExClassEvent[] $_ex_class_events */
      if (!array_key_exists($_key, $ex_class_events)) {
        $ex_class_events[$_key] = array();
      }

      $ex_class_events[$_key][] = $_ex_class_event;
    }
  }

  foreach ($_ex_class_creation as $_ex_class_id) {
    if (!isset($ex_class_events["$creation_context->_class/$_ex_class_id"])) {
      continue;
    }

    $_ex_class_events = $ex_class_events["$creation_context->_class/$_ex_class_id"];

    // TODO canCreateNew
    if ($creation_context) {
      foreach ($_ex_class_events as $_id => $_ex_class_event) {
        if (!$_ex_class_event->checkConstraints($creation_context)) {
          unset($_ex_class_events[$_id]);
        }
      }
    }

    if (count($_ex_class_events)) {
      $ex_classes_creation[$_ex_class_id] = $_ex_class_events;
    }
  }
}

if ($detail == 2) {
  foreach ($ex_objects as $_ex_class_id => $_ex_objects) {
    /** @var CExObject $first */
    $first = reset($_ex_objects);

    if (!$first) {
      continue;
    }

    $_ex_class = $first->_ref_ex_class;

    foreach ($_ex_class->_ref_groups as $_ex_group) {
      $_ex_group->_empty = true;

      foreach ($_ex_group->_ref_fields as $_ex_field) {
        $_ex_field->_empty = true;

        if ($_ex_field->hidden) {
          continue;
        }

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

// Création du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("reference_class",     $reference_class);
$smarty->assign("reference_id",        $reference_id);
$smarty->assign("cross_context_class", $cross_context_class);
$smarty->assign("cross_context_id",    $cross_context_id);
$smarty->assign("creation_context",    $creation_context);
$smarty->assign("reference",           $reference);
$smarty->assign("ex_objects",          $ex_objects);
$smarty->assign("ex_objects_counts",   $ex_objects_counts);
$smarty->assign("ex_objects_results",  $ex_objects_results);
$smarty->assign("limit",               $limit);
$smarty->assign("step",                $step);
$smarty->assign("total",               $total);
$smarty->assign("ex_classes_creation", $ex_classes_creation);
$smarty->assign("ex_classes",          CExClass::$_list_cache);
$smarty->assign("detail",              $detail);
$smarty->assign("ex_class_id",         $ex_class_id);
$smarty->assign("target_element",      $target_element);
$smarty->assign("other_container",     $other_container);
$smarty->assign("print",               $print);
$smarty->assign("start",               $start);
$smarty->assign("search_mode",         $search_mode);
$smarty->assign("readonly",            $readonly);
$smarty->display("inc_list_ex_object.tpl");
