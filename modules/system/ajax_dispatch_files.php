<?php
/**
 * Receive files EAI
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$profile = 0;

if ($profile) {
  xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY, array(
    'ignored_functions' => array(
      'call_user_func',
      'call_user_func_array',
    )
  ));
}

CCanDo::checkRead();

CApp::setTimeLimit(240);
CApp::setMemoryLimit("1024M");

$actor_guid   = CValue::get("actor_guid");
$to_treatment = CValue::get("to_treatment", 1);

/** @var CInteropSender $sender */
$sender = CMbObject::loadFromGuid($actor_guid);
$sender->loadRefGroup();
$sender->loadRefsExchangesSources();

$delete_file = $sender->_delete_file;

/** @var CExchangeSource $source */
$source = reset($sender->_ref_exchanges_sources);

if (!$source->active) {
  return;
}

$path = $source->getFullPath($source->_path);
$filename_excludes = "$path/mb_excludes.txt";

// Initialisation d'un fichier de verrou de 240 secondes
$lock = new CMbMutex("dispatch-files-$sender->_guid");

// On tente de verrouiller
if (!$lock->lock(240)) {
  return;
}

$count = $source->_limit = CAppUI::conf("eai max_files_to_process");

$files = array();
try {
  $files = $source->receive();
}
catch (CMbException $e) {
  CCronJobLog::$log = $e->getMessage();

  $e->stepAjax();
}

$fileextension           = $source->fileextension;
$fileextension_write_end = $source->fileextension_write_end;

$files_excludes = array();
if (file_exists($filename_excludes)) {
  $files_excludes = array_map('trim', file($filename_excludes));
}

$array_diff = array_diff($files_excludes, $files);

$files = array_diff($files, array($filename_excludes));
$files = array_diff($files, $files_excludes);
$files = array_slice($files, 0, $count);

// Mise à jour du fichier avec le nouveau diff
if (file_exists($filename_excludes)) {
  unlink($filename_excludes);
}  
$file  = fopen($filename_excludes, "a+");
foreach (array_diff($files_excludes, $array_diff) as $_file_exclude) {
  fputs($file, $_file_exclude."\n");
}

if (empty($files)) {
  CCronJobLog::$log = CAppUI::tr("CEAIDispatcher-no-file");
  CAppUI::stepAjax("CEAIDispatcher-no-file", UI_MSG_WARNING);

  // Libère le verrou
  $lock->release();
  return;
}

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

  //$_old_filepath = $_filepath;
  //$_filepath     = "$_filepath.checkedout";
  //$source->renameFile($_old_filepath, $_filepath);

  try {
    $message  = $source->getData($_filepath);
    if (!$message) {
      continue;
    }
  }
  catch (CMbException $e) {
    //$source->renameFile($_filepath, $_old_filepath);

    CCronJobLog::$log = $e->getMessage();
    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }

  $source->_receive_filename = $path_info["filename"];

  // Dispatch EAI 
  if ($acq = CEAIDispatcher::dispatch($message, $sender, null, $to_treatment)) {
    try {
      CEAIDispatcher::createFileACK($acq, $sender);
    }
    catch (CMbException $e) {
      if ($sender->_delete_file !== false) {
        $source->delFile($_filepath);
        if ($fileextension_write_end) {
          $source->delFile("$_filepath_no_ext.$fileextension_write_end");
        }
      }
      else {
        dispatchError($sender, $filename_excludes, $path_info);
      }

      CCronJobLog::$log = $e->getMessage();
      $e->stepAjax(UI_MSG_ERROR);
      continue;
    }
  }
  
  if (!$sender->delete_file) {
    CAppUI::stepAjax("CEAIDispatcher-message_dispatch");
    
    continue;
  }

  try {
    if ($sender->_delete_file !== false) {
      $source->delFile($_filepath);
      if ($fileextension_write_end) {
        $source->delFile("$_filepath_no_ext.$fileextension_write_end");
      }
    }
    else {
      dispatchError($sender, $filename_excludes, $path_info);
    }
  }
  catch (CMbException $e) {
    CCronJobLog::$log = $e->getMessage();
    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }  

  CAppUI::stepAjax("CEAIDispatcher-message_dispatch");
}

fclose($file);

// Libère le verrou
$lock->release();

if ($profile) {
  $xhprof_data = xhprof_disable();
  $xhprof_root = 'C:/xampp/htdocs/xhgui/';
  require_once $xhprof_root.'xhprof_lib/config.php';
  require_once $xhprof_root.'xhprof_lib/utils/xhprof_lib.php';
  require_once $xhprof_root.'xhprof_lib/utils/xhprof_runs.php';

  $xhprof_runs = new XHProfRuns_Default();
  $run_id = $xhprof_runs->save_run($xhprof_data, "mediboard");
}

function dispatchError(CInteropSender $sender, $filename_excludes, $path_info) {
  CAppUI::stepAjax("CEAIDispatcher-no_message_supported_for_this_actor", UI_MSG_WARNING, $sender->_data_format->_family_message->code);

  $file  = fopen($filename_excludes, "a");
  fputs($file, $path_info["dirname"]."/".$path_info["basename"]."\n");
  fclose($file);
}