<?php 

/**
 * move a file (id) to a mediboard object
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */
 
 CCanDo::checkEdit();

$file_id          = CValue::get("object_id");
$file_class       = CValue::get("object_class");
$destination_guid = CValue::get("destination_guid");

$allowed = array("CFile", "CCompteRendu");

if (!in_array($file_class, $allowed)) {
  return;
}

/** @var CFile|CCompteRendu $file */
$file = new $file_class();
$file->load($file_id);

$destination = CStoredObject::loadFromGuid($destination_guid);
if (($file->object_class == $destination->_id) && ($file->object_class == $destination->_class)) {
  return;
}
$file->setObject($destination);

if ($msg = $file->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg("CFile-msg-moved", UI_MSG_OK);
}
echo CAppUI::getMsg();