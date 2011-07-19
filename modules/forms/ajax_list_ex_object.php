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

CValue::setSession('reference_class', $reference_class);
CValue::setSession('reference_id',    $reference_id);

$reference = new $reference_class;

if ($reference_id) {
  $reference->load($reference_id);
}

CExClassField::$_load_lite = true;
CExObject::$_multiple_load = true;
CExObject::$_load_lite = $detail < 2;

$where = array();

if ($ex_class_id) {
  $where['ex_class_id'] = "= '$ex_class_id'";
}

$ex_class = new CExClass;
$ex_classes = $ex_class->loadList($where);

$all_ex_objects = array();
$ex_objects_by_event = array();
$ex_objects_counts_by_event = array();
$ex_classes_creation = array();
  
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
	
  $limit = null;
  
  switch($detail) {
    case 2: $limit = ($ex_class_id ? 30 : 20); break;
    case 1: $limit = ($ex_class_id ? 200 : 100); break;
	default:
    case 0: 
  }
	
  $_ex_objects = $_ex_object->loadList($where, "{$_ex_object->_spec->key} DESC", $limit);
  
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
  
  if ($_ex_class->host_class == $reference_class && !$_ex_class->disabled) {
    $ex_classes_creation[$ex_class_key][$_ex_class_id] = $_ex_class;
    
    if (count($_ex_objects) == 0){
      $ex_objects_by_event[$ex_class_key][$_ex_class_id] = array();
    }
  }
  
  if (isset($ex_objects_by_event[$ex_class_key][$_ex_class_id])) {
    krsort($ex_objects_by_event[$ex_class_key][$_ex_class_id]);
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
$smarty->assign("ex_classes_creation", $ex_classes_creation);
$smarty->assign("ex_classes",      $ex_classes);
$smarty->assign("detail",          $detail);
$smarty->assign("ex_class_id",     $ex_class_id);
$smarty->assign("target_element",  $target_element);
$smarty->display("inc_list_ex_object.tpl");
