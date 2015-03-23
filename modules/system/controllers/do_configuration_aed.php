<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$configs = CValue::post("c");
$object_guid = CValue::post("object_guid");

$object = null;

if ($object_guid && $object_guid != "global") {
  $object = CMbObject::loadFromGuid($object_guid);
  $object->needsRead();
}

// Strip slashed because added in store
$configs = stripslashes_deep($configs);

$messages = CConfiguration::setConfigs($configs, $object);

CConfiguration::clearDataCache();

foreach ($messages as $msg) {
  CAppUI::setMsg($msg, UI_MSG_WARNING);
}

CAppUI::setMsg("CConfiguration-msg-modify");

echo CAppUI::getMsg();
CApp::rip();
