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
$cv       = CValue::getOrSession("cv"); // concept values

CExClassField::$_load_lite = true;
CExObject::$_multiple_load = true;
CExObject::$_load_lite     = true;

$group_id = CGroups::loadCurrent()->_id;
$where = array(
  "group_id = $group_id OR group_id IS NULL"
);

$ex_class = new CExClass;
$from_cache = true;

$ex_classes = $ex_class->loadList($where, "name");
$ex_objects_counts_by_event = array();

foreach($ex_classes as $_ex_class_id => $_ex_class) {
  $ex_class_key = "$_ex_class->host_class-event-$_ex_class->event";
  
  $_ex_object = new CExObject;
  $_ex_object->_ex_class_id = $_ex_class_id;
  $_ex_object->setExClass();
  
  $where = array(
    "user_log.date" => "BETWEEN '$date_min' AND '$date_max'",
    "user_log.type" => "= 'create'",
  );
  
  $ljoin = array(
    "user_log" => "user_log.object_id = {$_ex_object->_spec->table}.ex_object_id AND user_log.object_class = '$_ex_object->_class'"
  );
  
  $_ex_objects_count = $_ex_object->countList($where, null, $ljoin);
  $_ex_objects_ids = $_ex_object->loadIds($where, null, null, null, $ljoin);
  
  if ($_ex_objects_count) {
    $ex_objects_counts_by_event[$_ex_class->host_class][$ex_class_key][$_ex_class_id] = array(
      "count" => $_ex_objects_count,
      "ids"   => $_ex_objects_ids,
    );
  }
}

// Création du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("ex_objects_counts_by_event", $ex_objects_counts_by_event);
$smarty->assign("ex_classes", $ex_classes);
$smarty->display("inc_list_ex_object_counts.tpl");
