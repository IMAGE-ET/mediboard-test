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

$step            = CValue::post("step");
$start           = CValue::post("start");
$directory       = CValue::post("directory");
$files_directory = CValue::post("files_directory");

if (!is_dir($directory)) {
  CAppUI::stepAjax("'%s' is not a directory", UI_MSG_WARNING, $directory);
  return;
}

if ($files_directory && !is_dir($files_directory)) {
  CAppUI::stepAjax("'%s' is not a directory", UI_MSG_WARNING, $files_directory);
  return;
}

$directory       = str_replace("\\\\", "\\", $directory);
$files_directory = str_replace("\\\\", "\\", $files_directory);

CValue::setSession("step", $step);
CValue::setSession("start", $start);
CValue::setSession("directory", $directory);
CValue::setSession("files_directory", $files_directory);

$step = min($step, 1000);

CStoredObject::$useObjectCache = false;

// Import ...
$iterator = new DirectoryIterator($directory);
$count_dirs = 0;

$i = 0;

foreach ($iterator as $_fileinfo) {
  if ($_fileinfo->isDot()) {
    continue;
  }

  if ($_fileinfo->isDir() && strpos($_fileinfo->getFilename(), "CPatient-") === 0) {
    $i++;
    if ($i <= $start) {
      continue;
    }

    if ($i > $start + $step) {
      break;
    }

    $count_dirs++;

    $xmlfile = $_fileinfo->getRealPath()."/export.xml";
    if (file_exists($xmlfile)) {
      $importer = new CPatientXMLImport($xmlfile);

      $importer->setDirectory($_fileinfo->getRealPath());

      if ($files_directory) {
        $importer->setFilesDirectory($files_directory);
      }

      $importer->import(array(), array());
    }
  }
}

CAppUI::stepAjax("%d patients trouvés à importer", UI_MSG_OK, $count_dirs);

if ($count_dirs) {
  CAppUI::js("nextStepPatients()");
}

