<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$guid    = CValue::get("guid");
$field   = CValue::get("field");
$options = CValue::get("options");

$object = CMbObject::loadFromGuid($guid);

if (!$object || !$object->canRead())
  return;

$result = "";

if ($field) {
  if ($options)
    $result = $object->getFormattedValue($field, $options);
  else
    $result = $object->$field;
  
  $result = utf8_encode($result);
}
else {
  $result = get_object_vars($object);
}

echo json_encode($result);