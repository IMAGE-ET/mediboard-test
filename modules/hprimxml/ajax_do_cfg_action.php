<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m;

$evenement = CValue::get("evenement");
$version = CAppUI::conf("hprimxml $evenement version");

switch ($evenement) {
  case "evt_serveuractes":
  	if ($version == "1.01") {
  	  extractFiles("serveurActes" , "schemaServeurActe_v101.zip");
  	} else if ($version == "1.05") {
  	  extractFiles("serveurActivitePmsi" , "schemaServeurActivitePmsi_v105.zip");
  	}
    break;
  
  case "evt_pmsi":
  	if ($version == "1.01") {
      extractFiles("evenementPmsi", "schemaPMSI_v101.zip" );
    } else if ($version == "1.05") {
      extractFiles("serveurActivitePmsi" , "schemaServeurActivitePmsi_v105.zip");
    }
    break;
    
  case "evt_patients":
  	if ($version == "1.05") {
      extractFiles("patients" , "schemaEvenementPatient_v105.zip", true);
    } else if ($version == "1.051") {
      extractFiles("patients" , "schemaEvenementPatient_v1051.zip", true);
    }
    break;
    
  case "evt_mvtStock":
    if ($version == "1.01") {
      extractFiles("mvtStock" , "schemaEvenementMvtStock_v101.zip");
    }
    break;
   
  default:
    echo "<div class='error'>Action '$evenement' inconnue</div>";
}

function extractFiles($schemaDir, $schemaFile, $delOldDir = false) {
  $baseDir = "modules/hprimxml/xsd";
  $destinationDir = "$baseDir/$schemaDir";
  $archivePath = "$baseDir/$schemaFile";
  if ($delOldDir && file_exists($destinationDir)) {
    if (CMbPath::remove($destinationDir)) {
      echo "<div class='message'>Suppression de $destinationDir</div>";
    } else {
      echo "<div class='error'>Impossible de supprimer le dossier $destinationDir</div>";
    }
  }
  if (false != $nbFiles = CMbPath::extract($archivePath, $destinationDir)) {
    echo "<div class='message'>Extraction de $nbFiles fichiers pour $schemaDir</div>";
  } else {
    echo "<div class='error'>Impossible d'extraire l'archive $schemaFile</div>";
  }
}

?>