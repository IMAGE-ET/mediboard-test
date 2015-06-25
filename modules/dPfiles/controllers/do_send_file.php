<?php 

/**
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

$object_class = CValue::post("object_class");
$object_id    = CValue::post("object_id");
$content      = CValue::post("content");

$file_name    = CValue::post("file_name");

$file = new CFile();
$file->file_name = $file_name;
$file->object_class = $object_class;
$file->object_id = $object_id;

$file->fillFields();
$file->putContent(base64_decode($content));
$file->file_type = CMbPath::guessMimeType($file_name);

if ($msg = $file->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg("CFile-msg-moved");
}

echo CAppUI::getMsg();