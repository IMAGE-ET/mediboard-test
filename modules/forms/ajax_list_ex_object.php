<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$reference_class = CValue::get("reference_class");
$reference_id    = CValue::get("reference_id");
$detail          = CValue::get("detail", 1);
$ex_class_id     = CValue::get("ex_class_id");
$target_element  = CValue::get("target_element");
$ex_object_ids   = CValue::get("ex_object_ids");
$print           = CValue::get("print");
$start           = CValue::get("start", 0);

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

$group_id = CGroups::loadCurrent()->_id;
$where = array(
  "group_id = $group_id OR group_id IS NULL"
);

if ($ex_class_id) {
  $where['ex_class_id'] = "= '$ex_class_id'";
}

$ex_class = new CExClass;
$from_cache = true;

if (empty(CExClass::$_list_cache)) {
  CExClass::$_list_cache = $ex_class->loadList($where);
  $from_cache = false;
}

$all_ex_objects = array();
$ex_objects_by_event = array();
$ex_classes_by_event = array();
$ex_objects_counts_by_event = array();
$ex_classes_creation = array();

$limit = null;

if ($print) {
  $limit = 5;
}
else {
  switch($detail) {
    case 3: 
    case 2: 
      $limit = ($ex_object_ids ? 50 : ($ex_class_id ? 15 : 10)); break;
    case 1: 
      $limit = ($ex_class_id ? 50 : 25); break;
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
  
foreach(CExClass::$_list_cache as $_ex_class_id => $_ex_class) {
  $ex_class_key = "$_ex_class->host_class-event-$_ex_class->event";
  
  if (!$from_cache && $detail > 1) {
    foreach($_ex_class->loadRefsGroups() as $_group) {
      $_group->loadRefsFields();
      foreach($_group->_ref_fields as $_field) {
        $_field->updateTranslation();
      }
    }
  }
  
  $_ex_object = new CExObject;
  $_ex_object->_ex_class_id = $_ex_class_id;
  $_ex_object->setExClass();
  
  if ($ex_object_ids) {
    $ids = explode("-", $ex_object_ids);
    $where = array(
      $_ex_object->_spec->key => $_ex_object->_spec->ds->prepareIn($ids),
    );
  }
  else {
    $where = array(
      "(reference_class  = '$reference_class' AND reference_id  = '$reference_id') OR 
       (reference2_class = '$reference_class' AND reference2_id = '$reference_id') OR 
       (object_class     = '$reference_class' AND object_id     = '$reference_id')"
    );
  }
  
  $_ex_objects = $_ex_object->loadList($where, "{$_ex_object->_spec->key} DESC", $limit);
  $_ex_objects_count = $_ex_object->countList($where);
  
  $total = max($_ex_objects_count, $total);
  
  if ($_ex_objects_count) {
    $ex_objects_counts_by_event[$ex_class_key][$_ex_class_id] = $_ex_objects_count;
  }
  
  if ($detail == 0) continue;
  
  foreach($_ex_objects as $_ex) {
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
    
    $all_ex_objects["$_log->date $_ex->_id"] = $_ex;
    $ex_objects_by_event[$ex_class_key][$_ex_class_id]["$_log->date $_ex->_id"] = $_ex;
  }

  if (!isset($ex_classes_creation[$ex_class_key])) {
    $ex_classes_creation[$ex_class_key] = array();
  }
  
  if ( $_ex_class->host_class == $reference_class && // Possible context
      !$_ex_class->disabled && // Not disabled
       $_ex_class->checkConstraints($reference) && // Passes constraints
       $_ex_class->canCreateNew($reference)
  ) { // Check unicity
    $ex_classes_creation[$ex_class_key][$_ex_class_id] = $_ex_class;
    
    if (count($_ex_objects) == 0){
      $ex_objects_by_event[$ex_class_key][$_ex_class_id] = array();
    }
  }
  
  if (isset($ex_objects_by_event[$ex_class_key][$_ex_class_id])) {
    krsort($ex_objects_by_event[$ex_class_key][$_ex_class_id]);
  }
}

if ($detail == 2) {
  foreach($ex_objects_by_event as $ex_objects_by_class) {
    foreach($ex_objects_by_class as $_ex_objects) {
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
}
  
ksort($ex_objects_by_event);
ksort($all_ex_objects);

// Création du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("reference_class", $reference_class);
$smarty->assign("reference_id",    $reference_id);
$smarty->assign("reference",       $reference);
$smarty->assign("all_ex_objects",  $all_ex_objects);
$smarty->assign("ex_objects_by_event", $ex_objects_by_event);
$smarty->assign("ex_objects_counts_by_event", $ex_objects_counts_by_event);
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
$smarty->assign("ex_object_ids",   $ex_object_ids);
$smarty->display("inc_list_ex_object.tpl");
