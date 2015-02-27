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

$file_id  = CValue::get("file_id");
$url      = CValue::get('url');

$format   = CValue::get('format');

if (!$file_id && !$url) {
  return "";
}

if ($file_id) {
  $file = new CFile();
  $file->load($file_id);
  $file->canDo();
  if (!$file->_can->read) {
    return "";
  }

  //@TODO le faire marcher avec du datauri
  if (strpos($file->file_type, "svg") !== false) {
    echo json_encode("?m=files&a=fileviewer&file_id=$file->_id&phpTumb=1&suppressHeaders=1");
    CApp::rip();
    //echo CApp::json(file_get_contents($file->_file_path));
  }
  elseif ($format == 'uri') {
    $data = $file->getDataURI();
    CApp::json($data);
  }
}
elseif ($url) {
  $mime_type = CMbPath::guessMimeType($url);
  $content = @file_get_contents($url);
  if ($content) {
    $data = "data:".$mime_type.";base64,".urlencode(base64_encode($content));
    CApp::json($data);
  }
  else {
    return "";
  }
}