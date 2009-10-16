<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m;

$evenement = mbGetValueFromGet("evenement");
$version = CAppUI::conf("hprimxml $evenement version");

switch ($evenement) {
  case "evt_serveuractes":
  	if ($version == "1.01") {
  	  extractFiles("evenementsServeurActes" , "schemaServeurActe_v101.zip");
  	} else if ($version == "1.05") {
  	  extractFiles("evenementsServeurActivitePmsi" , "schemaServeurActivitePmsi_v105.zip");
  	}
    break;
  
  case "evt_pmsi":
  	if ($version == "1.01") {
      extractFiles("evenementsPmsi", "schemaPMSI_v101.zip" );
    } else if ($version == "1.05") {
      extractFiles("evenementsServeurActivitePmsi" , "schemaServeurActivitePmsi_v105.zip");
    }
    break;
    
  case "evt_patients":
  	if ($version == "1.05") {
      extractFiles("evenementsPatients" , "schemaEvenementPatient_v105.zip");
    }
    break;
   
  default:
    echo "<div class='error'>Action '$evenement' inconnue</div>";
}

function extractFiles($schemaDir, $schemaFile) {
  $baseDir = "modules/hprimxml/xsd";
  $destinationDir = "$baseDir/$schemaDir";
  $archivePath = "$baseDir/$schemaFile";
  if (false != $nbFiles = CMbPath::extract($archivePath, $destinationDir)) {
    echo "<div class='message'>Extraction de $nbFiles fichiers pour $schemaDir</div>";
  } else {
    echo "<div class='error'>Impossible d'extraire l'archive $schemaFile</div>";
  }
}

?>