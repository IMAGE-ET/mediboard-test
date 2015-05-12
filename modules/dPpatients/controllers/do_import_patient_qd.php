<?php
/**
 * $Id: do_import_patient.php 4983 2013-12-12 17:09:40Z charly $
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GPLv2
 * @version    $Revision: 4983 $
 */

CCanDo::checkAdmin();
ini_set("auto_detect_line_endings", true);

global $dPconfig;
$dPconfig["object_handlers"] = array();
CAppUI::stepAjax("Désactivation du gestionnaire", UI_MSG_OK);

$start    = CValue::post("start");
$count    = CValue::post("count");
$callback = CValue::post("callback");
$date     = CMbDT::date();

CApp::setTimeLimit(600);
CApp::setMemoryLimit("512M");

CMbObject::$useObjectCache = false;

$file_import = fopen(CAppUI::conf("root_dir") . "/tmp/rapport_import_patient_$date.txt", "a");
importFile(CAppUI::conf("dPpatients imports pat_csv_path"), $start, $count, $file_import);
fclose($file_import);

$start += $count;
file_put_contents(CAppUI::conf("root_dir") . "/tmp/import_patient.txt", "$start;$count");

if ($callback) {
  CAppUI::js("$callback($start,$count)");
}

echo "<tr><td colspan=\"2\">MEMORY: " . memory_get_peak_usage(true) / (1024 * 1024) . " MB" . "</td>";
CMbObject::$useObjectCache = true;
CApp::rip();

/**
 * import the patient file
 *
 * @param string   $file        path to the file
 * @param int      $start       start int
 * @param int      $count       number of iterations
 * @param resource $file_import file for report
 *
 * @return null
 */
function importFile($file, $start, $count, $file_import) {
  $fp = fopen($file, 'r');

  $csv_file               = new CCSVFile($fp);
  $csv_file->column_names = $csv_file->readLine();

  if ($start == 0) {
    $start++;
  }
  elseif ($start > 1) {
    $csv_file->jumpLine($start);
  }

  $group_id = CGroups::loadCurrent()->_id;

  $treated_line = 0;
  while ($treated_line < $count) {
    $treated_line++;

    $patient  = new CPatient();
    $_patient = $csv_file->readLine(true);

    if (!$_patient) {
      CAppUI::stepAjax('Importation terminée', UI_MSG_OK);
      CApp::rip();
    }

    $patient->bind($_patient);
    $patient->loadFromIPP($group_id);

    if ($patient->_id) {
      $start++;
      continue;
    }

    $nom = ($_patient['nom']) ? $_patient['nom'] : $_patient['nom_jeune_fille'];

    if (!$patient->nom) {
      if ($patient->nom_jeune_fille) {
        $patient->nom = $patient->nom_jeune_fille;
      }
      else {
        CMbDebug::log("Ligne #{$start} : Pas de nom");
        $start++;
        continue;
      }
    }

    $naissance = null;
    if ($patient->naissance) {
      $naissance          = preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '\\3-\\2-\\1', $patient->naissance);
      $patient->naissance = $naissance;
    }

    $patient->repair();

    if (!$patient->naissance) {
      CMbDebug::log($_patient);
      CMbDebug::log("Ligne #{$start} : Date de naissance invalide ({$_patient['naissance']})");
      $start++;
      continue;
    }

    $patient->loadMatchingPatient();

    if (!$patient->_id) {
      $patient->bind($_patient);

      $patient->nom       = $nom;
      $patient->naissance = $naissance;

      $patient->tel       = preg_replace("/[^0-9]/", "", $patient->tel);
      $patient->tel_autre = preg_replace("/[^0-9]/", "", $patient->tel_autre);

      $patient->sexe = strtolower($patient->sexe);

      $patient->repair();

      if ($msg = $patient->store()) {
        CMbDebug::log($patient, null, true);
        CMbDebug::log("Ligne #{$start} :$msg");
        $start++;
        continue;
      }
    }

    $ipp = CIdSante400::getMatch($patient->_class, CPatient::getTagIPP($group_id), $patient->_IPP, $patient->_id);

    if ($ipp->_id && $ipp->id400 != $patient->_IPP) {
      CMbDebug::log("Ligne #{$start} : Ce patient possède déjà un IPP ({$ipp->id400})");
      $start++;
      continue;
    }

    if (!$ipp->_id) {
      if ($msg = $ipp->store()) {
        CMbDebug::log("Ligne #{$start} :$msg");
        $start++;
        continue;
      }
    }

    CAppUI::setMsg('CPatient-msg-create', UI_MSG_OK);
  }

  echo CAppUI::getMsg();
}