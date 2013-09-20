<?php 

/**
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$path = str_replace("..", "", CValue::post("path"));
$timed_picture_id = CValue::post("timed_picture_id");

if (is_file($path) && strpos(CSupervisionTimedPicture::PICTURES_ROOT, $path) !== 0) {
  $timed_picture = new CSupervisionTimedPicture();
  $timed_picture->load($timed_picture_id);

  $file = new CFile();
  $file->setObject($timed_picture);
  $file->fillFields();
  $file->file_name = basename($path);
  $file->file_size = filesize($path);
  $file->file_type = CMbPath::guessMimeType($path);
  $file->moveFile($path, false, true);
  if ($msg = $file->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg("Image enregistrée");
  }
}

echo CAppUI::getMsg();
CApp::rip();