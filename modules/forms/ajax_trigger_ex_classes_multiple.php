<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//CCanDo::checkAdmin();

$object_guids = CValue::get("object_guids");
$event        = CValue::get("event");

$ex_classes = array();
$group_id = CGroups::loadCurrent()->_id;

foreach($object_guids as $object_guid) {
  $object = CMbObject::loadFromGuid($object_guid);
  
  $ex_class = new CExClass;
  $ds = $ex_class->_spec->ds;
  
  $where = array(
    "host_class"  => $ds->prepare("=%", $object->_class),
    "event"       => $ds->prepare("=%", $event),
    "disabled"    => $ds->prepare("=%", 0),
    "conditional" => $ds->prepare("=%", 0),
    //"required"    => $ds->prepare("=%", 1),
    "group_id"    => $ds->prepare("=% OR group_id IS NULL", $group_id),
    //"ex_class_id" => $ds->prepareNotIn(array_keys($ex_classes)), // On exclut les formulaires deja dans le tableau
  );
  
  $_ex_classes = $ex_class->loadList($where);
  
  foreach($_ex_classes as $_id => $_ex_class) {
    if ($_ex_class->checkConstraints($object)) {
      $_ex_class->_host_object = $object;
      $ex_classes[] = $_ex_class;
    }
  }
}

$ex_classes_struct = array();

foreach($ex_classes as $_ex_class) {
  $ex_classes_struct[] = array(
    "ex_class_id" => $_ex_class->_id,
    "event"       => $event,
    "object_guid" => $_ex_class->_host_object->_guid,
  );
}

CApp::json($ex_classes_struct);
