<?php 
/**
 * Receive files EAI
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

CApp::setTimeLimit(240);
CApp::setMemoryLimit("512M");

$actor_guid   = CValue::get("actor_guid");
$to_treatment = CValue::get("to_treatment", 1);

$sender = CMbObject::loadFromGuid($actor_guid);
$sender->loadRefGroup();
$sender->loadRefsExchangesSources();

$delete_file = $sender->_delete_file;

$source = reset($sender->_ref_exchanges_sources);

$path = $source->getFullPath($source->_path);
$count = CAppUI::conf("eai max_files_to_process");

$filename_excludes = "$path/mb_excludes.txt";
$filename_lock     = "$path/mb_lock.txt";

if (file_exists($filename_lock)) {
  return;
}  

//// LOCK ////
touch($filename_lock);

$files_excludes = array();
if (file_exists($filename_excludes)) {
  $files_excludes = array_flip(array_map('trim', file($filename_excludes)));
}

while($count > 0 && ($_filepath = $source->receiveOne())) {
  // Fichier exclus car non géré par le Sender
  if (isset($files_excludes[$_filepath])) {
    continue;
  }
  
  $path_info = pathinfo($_filepath);
  if (!isset($path_info["extension"])) {
    continue;
  }

  $extension = $path_info["extension"];

  // Cas où l'extension voulue par la source FS est différente du fichier
  if ($source->fileextension && ($extension != $source->fileextension)) {
    continue;
  }
  
  $sender->_delete_file = $delete_file;
  
  // Recuperation du contenu du fichier
  try {
    $message = $source->getData($_filepath); 
    if (!$message) {
      continue;
    }
  } catch (CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }   
  
  $source->_receive_filename = $path_info["filename"];

  // Dispatch EAI 
  if ($acq = CEAIDispatcher::dispatch($message, $sender, null, $to_treatment)) {
    try {
      CEAIDispatcher::createFileACK($acq, $sender);
    } catch (Exception $e) {
      if ($sender->_delete_file !== false) {
        $source->delFile($_filepath);
      } 
      else {
        dispatchError($sender, $filename_excludes, $_filepath);
      }
      
      CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
    }
  }
  
  // Suppression du fichier
  try {
    if ($sender->_delete_file !== false) {
      $source->delFile($_filepath);
    }
    else {
      dispatchError($sender, $filename_excludes, $_filepath);
    }
  } catch (CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }  
  
  $count--;
  CAppUI::stepAjax("CEAIDispatcher-message_dispatch");
}

//// UNLOCK ////
unlink($filename_lock);

function dispatchError(CInteropSender $sender, $filename_excludes, $filepath) {
  CAppUI::stepAjax("CEAIDispatcher-no_message_supported_for_this_actor", UI_MSG_WARNING, $sender->_data_format->_family_message->code);

  $file = fopen($filename_excludes, "a");
  fwrite($file, "$filepath\n");
  fclose($file);
}

?>