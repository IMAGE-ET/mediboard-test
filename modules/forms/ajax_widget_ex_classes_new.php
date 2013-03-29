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

//CCanDo::checkAdmin();

$object_guid = CValue::get("object_guid");
$event_name  = CValue::get("event_name");
$_element_id = CValue::get("_element_id");

$object = CMbObject::loadFromGuid($object_guid);

//CExObject::$_load_lite = true;

$ex_class_event = new CExClassEvent;
$ds = $ex_class_event->_spec->ds;
$group_id = CGroups::loadCurrent()->_id;

$where = array(
  "ex_class_event.host_class"  => $ds->prepare("=%", $object->_class),
  "ex_class_event.event_name"  => $ds->prepare("=%", $event_name),
  //"ex_class_event.disabled"    => $ds->prepare("=%", 0),
  "ex_class.conditional"       => $ds->prepare("=%", 0),
  $ds->prepare("ex_class.group_id = % OR ex_class.group_id IS NULL", $group_id),
);
$ljoin = array(
  "ex_class" => "ex_class.ex_class_id = ex_class_event.ex_class_id",
);

/** @var CExClassEvent[] $ex_class_events */
$ex_class_events = $ex_class_event->loadList($where, null, null, null, $ljoin);
$ex_classes = array();
$ex_objects = array();

$count = 0;
$count_available = count($ex_class_events);
foreach ($ex_class_events as $_id => $_ex_class_event) {
  $_ex_class = $_ex_class_event->loadRefExClass();
  $_ex_class->getFormulaField();

  $ex_classes[$_ex_class->_id] = $_ex_class;

  /*if (!$_ex_class_event->checkConstraints($object)) {
    unset($ex_class_events[$_id]);
    $count_available--;
  }*/

  $_ex_objects = $_ex_class_event->getExObjectForHostObject($object);

  foreach ($_ex_objects as $_ex_object) {
    $_ex_object->load(); // Needed
    $_ex_object->loadLogs();
    foreach ($_ex_object->_ref_logs as $_log) {
      $_log->loadRefUser()->loadRefMediuser()->loadRefFunction();
    }
  }

  $count += count($_ex_objects);

  $ex_objects[$_ex_class->_id] = $_ex_objects;
}

foreach ($ex_objects as $_id => $_ex_object) {
  if (!count($_ex_object)) {
    unset($ex_objects[$_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_classes",      $ex_classes);
$smarty->assign("ex_objects",      $ex_objects);
$smarty->assign("object",          $object);
$smarty->assign("event_name",      $event_name);
$smarty->assign("count",           $count);
$smarty->assign("count_available", $count_available);
$smarty->assign("_element_id",     $_element_id);
$smarty->display("inc_widget_ex_classes_new.tpl");
