<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$configs = CValue::post("c");
$object_guid = CValue::post("object_guid");

$object = null;

if ($object_guid && $object_guid != "global") {
  $object = CMbObject::loadFromGuid($object_guid);
  $object->needsRead();
}

$messages = CConfiguration::setConfigs($configs, $object);

foreach ($messages as $msg) {
  CAppUI::setMsg($msg, UI_MSG_WARNING);
}

CAppUI::setMsg("CConfiguration-msg-modify");

echo CAppUI::getMsg();
CApp::rip();
