<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$file_id = CValue::get("file_id");
$format = CValue::get('format');

$file = new CFile();
$file->load($file_id);
$file->canDo();
if (!$file->_can->read) {
  return "";
}

if (strpos($file->file_type, "svg") !== false) {
  echo json_encode("?m=files&a=fileviewer&file_id=$file->_id&phpTumb=1");
  CApp::rip();
  //echo CApp::json(file_get_contents($file->_file_path));
}
elseif ($format == 'uri') {
  CApp::json($file->getDataURI());
}