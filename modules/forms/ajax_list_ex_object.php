<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$reference_class = CValue::get("reference_class");
$reference_id    = CValue::get("reference_id");
$detail          = CValue::get("detail", 1);
$ex_class_id     = CValue::get("ex_class_id");
$target_element  = CValue::get("target_element");
$print           = CValue::get("print");
$start           = CValue::get("start", 0);

CValue::setSession('reference_class', $reference_class);
CValue::setSession('reference_id',    $reference_id);

$reference = new $reference_class;

if ($reference_id) {
  $reference->load($reference_id);
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
$ex_classes = $ex_class->loadList($where);

$all_ex_objects = array();
$ex_objects_by_event = array();
$ex_objects_counts_by_event = array();
$ex_classes_creation = array();

$limit = null;

if ($print) {
  $limit = 50;
}
else {
  switch($detail) {
    case 2: $limit = ($ex_class_id ? 20 : 15); break;
    case 1: $limit = ($ex_class_id ? 100 : 50); break;
  default:
    case 0: 
  }
}

$step = $limit;
$total = 0;

if ($limit) {
  $limit = "$start, $limit";
}
  
foreach($ex_classes as $_ex_class_id => $_ex_class) {
  $ex_class_key = $_ex_class->host_class."-".$_ex_class->event;
    
  if ($detail == 2) {
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
  
  $where = array(
    "(reference_class  = '$reference_class' AND reference_id  = '$reference_id') OR 
     (reference2_class = '$reference_class' AND reference2_id = '$reference_id') OR 
     (object_class     = '$reference_class' AND object_id     = '$reference_id')"
  );
  
  $_ex_objects = $_ex_object->loadList($where, "{$_ex_object->_spec->key} DESC", $limit);
  $_ex_objects_count = $_ex_object->countList($where);
  $total = max($_ex_objects_count, $total);
  
  $ex_objects_counts_by_event[$ex_class_key][$_ex_class_id] = $_ex_object->countList($where);
  
  foreach($_ex_objects as $_ex) {
    $_ex->_ex_class_id = $_ex_class_id;
    $_ex->load();
    $_ex->loadTargetObject();
    
    // to get the view
    $_ex->_ref_object->loadComplete();
    
    $_ex->loadLogs();
    $_log = $_ex->_ref_first_log;
    
    $all_ex_objects["$_log->date $_ex->_id"] = $_ex;
    $ex_objects_by_event[$ex_class_key][$_ex_class_id]["$_log->date $_ex->_id"] = $_ex;
  }
  
  if (!isset($ex_classes_creation[$ex_class_key])) {
    $ex_classes_creation[$ex_class_key] = array();
  }
  
  if ($_ex_class->host_class == $reference_class && !$_ex_class->disabled && $_ex_class->checkConstraints($reference)) {
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
$smarty = new CSmartyDP();
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
$smarty->assign("ex_classes",      $ex_classes);
$smarty->assign("detail",          $detail);
$smarty->assign("ex_class_id",     $ex_class_id);
$smarty->assign("target_element",  $target_element);
$smarty->assign("print",           $print);
$smarty->assign("start",           $start);
$smarty->display("inc_list_ex_object.tpl");
