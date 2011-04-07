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
$ex_class->host_class = $object->_class_name;
$ex_class->event = $event;
$ex_class->disabled = 0;
$ex_class->conditional = 0;
$ex_class->required = 1;

$ex_classes = $ex_class->loadMatchingList();

foreach($ex_classes as $_id => $_ex_class) {
  if (!$_ex_class->checkConstraints($object)) {
    unset($ex_classes[$_id]);
  }
}

CApp::json(array_keys($ex_classes));
