<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$evenement = CValue::get("evenement");
$version = CAppUI::conf("hprimxml $evenement version");

$status = 0;
$file   = null;
$racine = "modules/hprimxml/xsd/";

if ($evenement == "evt_serveuractes") {
  if ($version == "1.01") {
    $file = $racine."serveurActes/msgEvenementsServeurActes101.xsd";
  } else if ($version == "1.05") {
    $file = $racine."serveurActivitePmsi/msgEvenementsServeurActes105.xsd";
  }
}

if ($evenement == "evt_pmsi") {
  if ($version == "1.01") {
    $file = $racine."evenementPmsi/msgEvenementsPmsi101.xsd";
  } else if ($version == "1.05") {
    $file = $racine."serveurActivitePmsi/msgEvenementsPmsi105.xsd";
  }
}

if ($evenement == "evt_serveuretatspatient") {
  if ($version == "1.05") {
    $file = $racine."serveurActivitePmsi/msgEvenementsServeurEtatsPatient105.xsd";
  }
}

if ($evenement == "evt_patients") {
  $version = str_replace(".", "", $version);
  $file = $racine."patients/msgEvenementsPatients$version.xsd";
}

if ($evenement == "evt_mvtStock") {
  $version = str_replace(".", "", $version);
  $file = $racine."mvtStock/msgEvenementsMvtStocks$version.xsd";
}

if (file_exists($file)) {
  $status = 1; 
}

if ($status)
  echo '<div class="message">Fichiers présents</div>';
else
  echo '<div class="error">Fichiers manquants</div>';


?>