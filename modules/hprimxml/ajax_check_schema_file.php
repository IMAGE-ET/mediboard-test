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
    $file = $racine."evenementsServeurActes/msgEvenementsServeurActes101.xsd";
  } else if ($version == "1.05") {
    $file = $racine."evenementsServeurActivitePmsi/msgEvenementsServeurActes105.xsd";
  }
}

if ($evenement == "evt_pmsi") {
  if ($version == "1.01") {
    $file = $racine."evenementsPmsi/msgEvenementsPmsi101.xsd";
  } else if ($version == "1.05") {
    $file = $racine."evenementsServeurActivitePmsi/msgEvenementsPmsi105.xsd";
  }
}

if ($evenement == "evt_patients") {
  if ($version == "1.05") {
   $file = $racine."evenementsPatients/msgEvenementsPatients105.xsd";
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