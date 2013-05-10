<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage eai
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$actor_guid  = CValue::getOrSession("actor_guid");

if ($actor_guid) {
  $actor = CMbObject::loadFromGuid($actor_guid);
  if ($actor->_id) {
    $objects = $actor->loadRefObjectConfigs();
    $linked_objects = array();

    foreach ($objects as $_object) {
      $_class = $_object->object_class;
      if (!array_key_exists($_class, $linked_objects)) {
        $linked_objects[$_class] = array();
      }
      $_object->loadRefObject();
      $linked_objects[$_class][] = $_object;
    }

    $objects = $actor->loadBackRefs("object_links");

    $classes = CApp::getChildClasses();

    $linked_object = new CObjectToInteropSender();
    $linked_object->sender_class = $actor->_class;
    $linked_object->sender_id = $actor->_id;

    $smarty = new CSmartyDP();
    $smarty->assign("actor", $actor);
    $smarty->assign("linked_objects", $linked_objects);
    $smarty->assign("classes", $classes);
    $smarty->assign("linked_object", $linked_object);
    $smarty->display("inc_linked_objects.tpl");
  }
}