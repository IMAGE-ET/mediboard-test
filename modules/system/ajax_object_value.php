<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$guid  = CValue::get("guid");
$field = CValue::get("field");

$object = CMbObject::loadFromGuid($guid);

if (!$object || !$object->canRead())
  CApp::rip();

if ($field)
  echo json_encode($object->$field);
else
  echo json_encode(get_object_vars($object));
