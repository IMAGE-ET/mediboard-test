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

$file_id = CValue::get("file_id");
$destinationGuid = CValue::get("destinationGuid");

$file = new CFile();
$file->load($file_id);

$destination = CStoredObject::loadFromGuid($destinationGuid);

$file->object_id = $destination->_id;
$file->object_class = $destination->_class;

if ($msg = $file->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg("CFile-msg-moved", UI_MSG_OK);
}

echo CAppUI::getMsg();