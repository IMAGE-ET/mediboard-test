<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$class = CValue::get("object_class");
$field = CValue::get("field");

$object = new $class();

do {
  $object->{$field} = CPasswordSpec::randomString(array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z')), 8);
}
while ($object->_specs[$field]->checkProperty($object));

echo json_encode($object->{$field});