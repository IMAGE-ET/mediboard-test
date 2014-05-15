<?php
/**
 * Move a file (id) to a mediboard object
 *
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$file_id          = CValue::get("object_id");
$file_class       = CValue::get("object_class");
$destination_guid = CValue::get("destination_guid");
$name             = CValue::get("file_name");
$category_id      = CValue::get("category_id");

$allowed = array("CFile", "CCompteRendu");

if (!in_array($file_class, $allowed)) {
  CAppUI::stepAjax("CFile-msg-not_allowed_object_to_move", UI_MSG_ERROR);
}

/** @var CFile|CCompteRendu $file */
$file = new $file_class();
$file->load($file_id);
$file->file_category_id = ($category_id != $file->file_category_id) ? $category_id : $file->file_category_id;
$file->file_name = $name ? $name : $file->file_name;

$destination = CStoredObject::loadFromGuid($destination_guid);
if (($file->object_id == $destination->_id) && ($file->object_class == $destination->_class)) {
  CAppUI::stepAjax("CFile-msg-from_equal_to", UI_MSG_ERROR);
}
$file->setObject($destination);


if ($msg = $file->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg("CFile-msg-moved");
}
echo CAppUI::getMsg();
