<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_guid  = CValue::get("object_guid");
$remove_empty_values = CValue::get("remove_empty_values");

$object = CMbObject::loadFromGuid($object_guid);

try {
	$export = new CMbObjectExport($object);
}
catch (CMbException $e) {
	$e->stepAjax(UI_MSG_ERROR);
}

$export->empty_values = !$remove_empty_values;
$export->streamXML();
