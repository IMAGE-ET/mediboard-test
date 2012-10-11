<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//CCanDo::checkAdmin();

$object_guid = CValue::get("object_guid");
$event_name  = CValue::get("event_name");

$object = CMbObject::loadFromGuid($object_guid);

$ex_class_event = new CExClassEvent;
$ds = $ex_class_event->_spec->ds;
$group_id = CGroups::loadCurrent()->_id;

$where = array(
  "ex_class_event.host_class"  => $ds->prepare("=%", $object->_class),
  "ex_class_event.event_name"  => $ds->prepare("=%", $event_name),
  "ex_class_event.disabled"    => $ds->prepare("=%", 0),
  "ex_class.conditional"       => $ds->prepare("=%", 0),
  $ds->prepare("ex_class.group_id = % OR ex_class.group_id IS NULL", $group_id),
);
$ljoin = array(
  "ex_class" => "ex_class.ex_class_id = ex_class_event.ex_class_id",
);
$ex_class_events = $ex_class_event->loadList($where, null, null, null, $ljoin);

$ex_class_events_struct = array();

foreach($ex_class_events as $_ex_class_event) {
  if ($_ex_class_event->checkConstraints($object)) {
    $ex_class_events_struct[] = array(
      "ex_class_event_id" => $_ex_class_event->_id,
      "ex_class_id" => $_ex_class_event->ex_class_id,
      "event_name"  => $event_name,
      "object_guid" => $object_guid,
    );
  }
}

CApp::json($ex_class_events_struct);
