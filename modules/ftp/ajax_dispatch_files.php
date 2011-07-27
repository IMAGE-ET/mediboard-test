<?php 
/**
 * Receive files EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$sender_ftp_guid = CValue::get("sender_ftp_guid");

$sender_ftp = CMbObject::loadFromGuid($sender_ftp_guid);
$sender_ftp->loadRefGroup();
$sender_ftp->loadRefsExchangesSources();

$source_ftp = reset($sender_ftp->_ref_exchanges_sources);

$files = array();
try {
  $files = $source_ftp->receive();
} catch (CMbException $e) {
  $e->stepAjax();
}

$fileextension           = $source_ftp->fileextension;
$fileextension_write_end = $source_ftp->fileextension_write_end;
foreach ($files as $_filepath) {
  $path_info = pathinfo($_filepath);
  
  // Cas où l'extension voulue par la source FTP est différente du fichier
  if ($fileextension && ($path_info["extension"] != $fileextension)) {
    continue;
  }
  
  $_filepath_no_ext = $path_info["dirname"]."/".$path_info["filename"];
    // Cas où le suffix de l'acq OK est présent mais que je n'ai pas de fichier 
  // d'acquittement dans le dossier
  if ($fileextension_write_end && 
      (($path_info["extension"] == $fileextension_write_end) || 
      !preg_grep("@^$_filepath_no_ext.$fileextension_write_end$@", $files))) {
    continue;
  }

  $message  = $source_ftp->getData($_filepath);  
  if (!$message) {
    continue;
  }
  
  // Dispatch EAI 
  if (!CEAIDispatcher::dispatch($message, $sender_ftp)) {
    CEAIDispatcher::createFileACQ($message, $sender_ftp);
  }
}

?>