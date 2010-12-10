<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//CCanDo::checkAdmin();

$object_guid = CValue::get("object_guid");
$event       = CValue::get("event");
$_element_id = CValue::get("_element_id");

$object = CMbObject::loadFromGuid($object_guid);

$ex_class = new CExClass;
$ex_class->host_class = $object->_class_name;
$ex_class->event = $event;
$ex_classes = $ex_class->loadMatchingList();

$ex_objects = array();

$count = 0;
foreach($ex_classes as $_id => $_ex_class) {
  if (!$_ex_class->checkConstraints($object)) {
    unset($ex_classes[$_id]);
  }
  
  $objects = $_ex_class->loadExObjects($object);
  $count += count($objects);
  
  /*foreach($objects as $_object) {
    $_object->loadLogs();
  }*/
  
  $ex_objects[$_ex_class->_id] = $objects;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_classes", $ex_classes);
$smarty->assign("ex_objects", $ex_objects);
$smarty->assign("object", $object);
$smarty->assign("event", $event);
$smarty->assign("count", $count);
$smarty->assign("_element_id", $_element_id);
$smarty->display("inc_widget_ex_classes.tpl");
