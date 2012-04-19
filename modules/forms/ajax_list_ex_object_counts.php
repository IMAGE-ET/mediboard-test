<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$date_min = CValue::getOrSession("date_min");
$date_max = CValue::getOrSession("date_max");
$group_id = CValue::getOrSession("group_id");
$concept_search = CValue::get("concept_search"); // concept values

CExClassField::$_load_lite = true;
CExObject::$_multiple_load = true;
CExObject::$_load_lite     = true;

$ex_class = new CExClass;

$where = array(
  "group_id = $group_id OR group_id IS NULL"
);
$ljoin = array();

if ($concept_search) {
  $concept_search = stripslashes($concept_search);
  $search = CExConcept::parseSearch($concept_search);
  
  if (!empty($search)) {
    $ds = $ex_class->_spec->ds;
    $where["ex_class_field.concept_id"] = $ds->prepareIn(array_keys($search));
    
    $ljoin = array(
      "ex_class_field_group" => "ex_class_field_group.ex_class_id = ex_class.ex_class_id",
      "ex_class_field"       => "ex_class_field.ex_group_id = ex_class_field_group.ex_class_field_group_id",
    );
  }
}

$ex_classes = $ex_class->loadList($where, "name", null, null, $ljoin);
$ex_objects_counts_by_event = array();

foreach($ex_classes as $_ex_class_id => $_ex_class) {
  $ex_class_key = "$_ex_class->host_class-event-$_ex_class->event";

  $_ex_object = new CExObject;
  $_ex_object->_ex_class_id = $_ex_class_id;
  $_ex_object->setExClass();

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

  $_ex_objects_count = $_ex_object->countList($where, null, $ljoin);

  if ($_ex_objects_count) {
    $ex_objects_counts_by_event[$_ex_class->host_class][$ex_class_key][$_ex_class_id] = $_ex_objects_count;
  }
}

// Création du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("ex_objects_counts_by_event", $ex_objects_counts_by_event);
$smarty->assign("ex_classes", $ex_classes);
$smarty->display("inc_list_ex_object_counts.tpl");
