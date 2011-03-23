<?php 
/**
 * Messages supported
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid          = CValue::getOrSession("actor_guid");
$exchange_class_name = CValue::getOrSession("exchange_class_name");

$actor = CMbObject::loadFromGuid($actor_guid);
list($object_class, $object_id) = explode("-", $actor_guid);

$class    = new ReflectionClass($exchange_class_name);
$statics  = $class->getStaticProperties();

$messages = array();
foreach ($statics["messages"] as $_message => $_evt_class) {
  $class = new ReflectionClass($_evt_class);
  $statics = $class->getStaticProperties();
  
  foreach ($statics["evenements"] as $_evt) {
    $message_supported = new CMessageSupported();
    $message_supported->object_class = $object_class;
    $message_supported->object_id    = $object_id;
    $message_supported->message      = $_evt;
    $message_supported->loadMatchingObject();
    $messages[$_evt_class][] = $message_supported;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("messages"    , $messages);
$smarty->display("inc_messages_supported.tpl");

?>