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

$object = CMbObject::loadFromGuid($object_guid);

mbTrace($object->_view);

$ex_class = new CExClass;
$ex_class->host_class = $object->_class_name;
$ex_class->event = $event;
$ex_classes = $ex_class->loadMatchingList();

mbTrace(count($ex_classes), "ex_classes_before", true);

foreach($ex_classes as $_id => $_ex_class) {
  if (!$_ex_class->checkConstraints($object)) {
    unset($ex_classes[$_id]);
  }
}

mbTrace(count($ex_classes), "ex_classes_after", true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_classes", $ex_classes);
$smarty->assign("object", $object);
$smarty->display("inc_vw_available_ex_classes.tpl");
