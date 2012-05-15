<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$configs = CValue::post("c");
$object_guid = CValue::post("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

CConfiguration::setConfigs($configs, $object);

echo CAppUI::getMsg();
CApp::rip();
