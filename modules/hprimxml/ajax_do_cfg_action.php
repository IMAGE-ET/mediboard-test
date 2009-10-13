<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m;

$action = mbGetValueFromGet("action");

switch ($action) {
  case "evenementsServeurActes":
    extractFiles("evenementsServeurActes" , "schemaServeurActe_v101.zip");
    break;
  
  case "evenementsPmsi":
    extractFiles("evenementsPmsi", "schemaPMSI_v101.zip" );
    
  case "evenementsPatients":
    extractFiles("evenementsPatients" , "schemaEvenementPatient_v105.zip");
    break;

  default:
    echo "<div class='error'>Action '$action' inconnue</div>";
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