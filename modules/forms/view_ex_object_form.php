<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$ex_class_id  = CValue::get("ex_class_id");
$ex_object_id = CValue::get("ex_object_id");
$object_guid  = CValue::get("object_guid");
$_element_id  = CValue::get("_element_id");
$event        = CValue::get("event");
$parent_view  = CValue::get("parent_view");

$readonly     = CValue::get("readonly");
$print        = CValue::get("print");
$autoprint    = CValue::get("autoprint");
$only_filled  = CValue::get("only_filled");
$noheader     = CValue::get("noheader");

if (!$ex_class_id) {
  $msg = "Impossible d'afficher le formulaire sans connaître la classe de base";
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
  trigger_error($msg, E_USER_ERROR);
  return;
}

$preview = $object_guid === "preview";

if ($preview) {
  $ex_class = new CExClass;
  $ex_class->load($ex_class_id);
  $object = new $ex_class->host_class;
}
else {
  $object = CMbObject::loadFromGuid($object_guid);
  $object->loadComplete();
}

if (!$ex_object_id) {
  $ex_class = new CExClass;
  $ex_class->load($ex_class_id);
  $ex_objects = $ex_class->getExObjectForHostObject($object);
  
  $ex_object = reset($ex_objects);
}
else {
  $ex_object = new CExObject;
}

if (!$ex_object) {
  $ex_object = $ex_class->getExObjectInstance();
}
  
$ex_object->setObject($object);
$ex_object->_ex_class_id = $ex_class_id;
$ex_object->setExClass();

list($grid, $out_of_grid, $groups) = $ex_object->_ref_ex_class->getGrid();

/*foreach($groups as $_group) {
  foreach($_group->_ref_fields as $_field) {
    $_field->loadTriggeredData();
  }
}*/

if ($ex_object_id || $ex_object->_id) {
  $ex_object->load($ex_object_id);
}
else {
  $ex_object->group_id = CGroups::loadCurrent()->_id;
}

$ex_object->loadRefGroup();

// loadAllFwdRefs ne marche pas bien (a cause de la clé primaire)
foreach($ex_object->_specs as $_field => $_spec) {
  if ($_spec instanceof CRefSpec && $_field != $ex_object->_spec->key) {
    $class = $_spec->meta ? $ex_object->{$_spec->meta} : $_spec->class;
    
    if (!$class) continue;
    
    $obj = new $class;
    $obj->load($ex_object->$_field);
    $ex_object->_fwd[$_field] = $obj;
  }
}

$ex_object->getReportedValues();
$ex_object->loadRefReferenceObjects();

if (!$ex_object->_id) {
  if (!$ex_object->reference_id && !$ex_object->reference_class) {
    $reference = $ex_class->resolveReferenceObject($object, 1);
    $ex_object->setReferenceObject_1($reference);
  }
  
  if (!$ex_object->reference2_id && !$ex_object->reference2_class) {
    $reference = $ex_class->resolveReferenceObject($object, 2);
    $ex_object->setReferenceObject_2($reference);
  }
}

$fields = array();
foreach($groups as $_group) {
  $fields = array_merge($_group->_ref_fields, $fields);
  
  foreach($_group->_ref_host_fields as $_host_field) {
    switch ($_host_field->host_type) {
      case "host":
        $_host_field->_ref_host_object = $object; 
      break;
      
      case "reference1":
        $_host_field->_ref_host_object = $ex_object->_ref_reference_object_1;
      break;
      
      case "reference2":
        $_host_field->_ref_host_object = $ex_object->_ref_reference_object_2;
      break;
    }
  }
}

foreach($fields as $_field) {
  $_field->loadTriggeredData();
}

$ex_object->_rel_patient = null;
if (in_array("IPatientRelated", class_implements($ex_object->object_class))) {
  if ($ex_object->_ref_object->_id) {
    $rel_patient = $ex_object->_ref_object->loadRelPatient();
    $rel_patient->loadIPP();
  }
  else {
    $rel_patient = new CPatient;
    
    if ($preview) {
      $rel_patient->_view = "Patient exemple";
      $rel_patient->_IPP = "0123456";
      $ex_object->_ref_object->_view = CAppUI::tr($ex_object->_ref_object->_class)." test";
    }
  }
  
  $ex_object->_rel_patient = $rel_patient;
}

if ($ex_object->_ref_reference_object_1 instanceof CPatient) {
  $ex_object->_ref_reference_object_1->loadIPP();
}

if ($ex_object->_ref_reference_object_2 instanceof CPatient) {
  $ex_object->_ref_reference_object_2->loadIPP();
}

$formula_token_values = array();
foreach($fields as $_field) {
  $formula_token_values[$_field->name] = array(
    "values"  => $_field->getFormulaValues(),
    "formula" => $_field->formula,
    "formulaView" => utf8_encode($_field->_formula),
  );
}

$can_delete = false;

if ($ex_object->_id) {
  $can_delete = ($ex_object->loadFirstLog()->user_id == CUser::get()->_id);
}

$can_delete = $can_delete || CModule::getInstalled("forms")->canAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_object",    $ex_object);
$smarty->assign("ex_object_id", $ex_object_id);
$smarty->assign("ex_class_id",  $ex_class_id);
$smarty->assign("object_guid",  $object_guid);
$smarty->assign("object",       $object);
$smarty->assign("_element_id",  $_element_id);
$smarty->assign("event",        $event);
$smarty->assign("grid",         $grid);
$smarty->assign("out_of_grid",  $out_of_grid);
$smarty->assign("groups",       $groups);
$smarty->assign("formula_token_values", $formula_token_values);
$smarty->assign("can_delete",   $can_delete);
$smarty->assign("parent_view",  $parent_view);

$smarty->assign("readonly",     $readonly);
$smarty->assign("print",        $print);
$smarty->assign("autoprint",    $autoprint);
$smarty->assign("only_filled",  $only_filled);
$smarty->assign("noheader",     $noheader);
$smarty->display("view_ex_object_form.tpl");
