<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$evenement = mbGetValueFromGet("evenement");
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

if ($evenement == "evt_patients") {
  if ($version == "1.05") {
   $file = $racine."patients/msgEvenementsPatients105.xsd";
  }
}

if ($evenement == "evt_mvtStock") {
  if ($version == "1.01") {
   $file = $racine."mvtStock/msgEvenementsMvtStocks101.xsd";
  }
}

if (file_exists($file)) {
  $status = 1; 
}

if ($status)
  echo '<div class="message">Fichiers présents</div>';
else
  echo '<div class="error">Fichiers manquants</div>';


?>