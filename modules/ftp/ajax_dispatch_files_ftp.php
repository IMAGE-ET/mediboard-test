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

$actor_guid   = CValue::get("actor_guid");
$to_treatment = CValue::get("to_treatment", 1);

$sender = CMbObject::loadFromGuid($actor_guid);
$sender->loadRefGroup();
$sender->loadRefsExchangesSources();

$delete_file = $sender->_delete_file;

$source = reset($sender->_ref_exchanges_sources);

$files = array();
try {
  $files = $source->receive();
} catch (CMbException $e) {
  $e->stepAjax();
}

$fileextension           = $source->fileextension;
$fileextension_write_end = $source->fileextension_write_end;

foreach ($files as $_filepath) {
  $sender->_delete_file = $delete_file;
  
  $path_info = pathinfo($_filepath);
  if (!isset($path_info["extension"])) {
    continue;
  }
  
  $extension = $path_info["extension"];

  // Cas où l'extension voulue par la source FS est différente du fichier
  if ($fileextension && ($extension != $fileextension)) {
    continue;
  }

  $path = rtrim($path_info["dirname"], "\\/");
  $_filepath_no_ext = "$path/".$path_info["filename"];
  
  // Cas où le suffix de l'acq OK est présent mais que je n'ai pas de fichier 
  // d'acquittement dans le dossier
  if ($fileextension_write_end && count(preg_grep("@$_filepath_no_ext.$fileextension_write_end$@", $files)) == 0) {
    continue;
  }

  $_old_filepath = $_filepath;
  $_filepath     = "$_filepath.checkedout";
  $source->renameFile($_old_filepath, $_filepath);

  try {
    $message  = $source->getData($_filepath);
    if (!$message) {
      continue;
    }
  }
  catch (CMbException $e) {
    $source->renameFile($_filepath, $_old_filepath);

    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }   
  
  $source->_receive_filename = $path_info["filename"];

  // Dispatch EAI 
  if ($acq = CEAIDispatcher::dispatch($message, $sender, null, $to_treatment)) {
    try {
      CEAIDispatcher::createFileACK($acq, $sender);
    }
    catch (Exception $e) {
      if ($sender->_delete_file !== false) {
        $source->delFile($_filepath);
      }
      else {
        CAppUI::stepAjax("CEAIDispatcher-error_deleting_file", UI_MSG_WARNING);
      } 
      CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
    }
  }
  
  if (!$sender->delete_file) {
    CAppUI::stepAjax("CEAIDispatcher-message_dispatch");
    continue;
  }

  try {
    if ($sender->_delete_file !== false) {
      $source->delFile($_filepath);
    }
    else {
      CAppUI::stepAjax("CEAIDispatcher-error_deleting_file", UI_MSG_WARNING);
    }
  } 
  catch (CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }  
  
  CAppUI::stepAjax("Message retraité");
}