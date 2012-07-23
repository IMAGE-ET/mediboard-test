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
$ext    = (CAppUI::conf("hprimxml concatenate_xsd")) ? "xml" : "xsd";

if ($evenement == "evt_serveuractes") {
  if ($version == "1.01") {
    $file = $racine."serveurActes/msgEvenementsServeurActes101.$ext";
  } 
  else {
    $version = str_replace(".", "", $version);
    $file = $racine."serveurActivitePmsi_v$version/msgEvenementsServeurActes$version.$ext";
  }
}

if ($evenement == "evt_pmsi") {
  if ($version == "1.01") {
    $file = $racine."evenementPmsi/msgEvenementsPmsi101.$ext";
  } 
  else {
    $version = str_replace(".", "", $version);
    $file = $racine."serveurActivitePmsi_v$version/msgEvenementsPmsi$version.$ext";
  }
}

if ($evenement == "evt_serveuretatspatient") {
  $version = str_replace(".", "", $version);
  $file = $racine."serveurActivitePmsi_v$version/msgEvenementsServeurEtatsPatient$version.$ext";
}

if ($evenement == "evt_frais_divers") {
  $version = str_replace(".", "", $version);
  $file = $racine."serveurActivitePmsi_v$version/msgEvenementsFraisDivers$version.$ext";
}

if ($evenement == "evt_serveurintervention") {
  $version = str_replace(".", "", $version);
  $file = $racine."serveurActivitePmsi_v$version/msgEvenementsServeurActes$version.$ext";
}

if ($evenement == "evt_patients") {
  $version = str_replace(".", "", $version);
  $file = $racine."patients/msgEvenementsPatients$version.$ext";
}

if ($evenement == "evt_mvtStock") {
  $version = str_replace(".", "", $version);
  $file = $racine."mvtStock/msgEvenementsMvtStocks$version.$ext";
}

if (file_exists($file)) {
  $status = 1; 
}

if ($status)
  echo '<div class="info">Fichiers présents</div>';
else
  echo '<div class="error">Fichiers manquants</div>';


?>