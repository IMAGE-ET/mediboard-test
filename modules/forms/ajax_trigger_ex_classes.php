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
$event       = CValue::get("event");

$object = CMbObject::loadFromGuid($object_guid);

$ex_class = new CExClass;
$ds = $ex_class->_spec->ds;
$group_id = CGroups::loadCurrent()->_id;

$where = array(
  "host_class"  => $ds->prepare("=%", $object->_class),
  "event"       => $ds->prepare("=%", $event),
  "disabled"    => $ds->prepare("=%", 0),
  "conditional" => $ds->prepare("=%", 0),
  //"required"    => $ds->prepare("=%", 1),
  "group_id"    => $ds->prepare("=% OR group_id IS NULL", $group_id),
);
$ex_classes = $ex_class->loadList($where);

foreach($ex_classes as $_id => $_ex_class) {
  if (!$_ex_class->checkConstraints($object)) {
    unset($ex_classes[$_id]);
  }
}

CApp::json(array_keys($ex_classes));
