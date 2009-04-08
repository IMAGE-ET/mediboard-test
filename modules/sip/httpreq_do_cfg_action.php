<?php 
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$action = mbGetValueFromGet("action");

switch ($action) {
  case "extractFiles":
    extractFiles("evenementPatient" , "schemaHprimXmlEvenementPatientV1_05.zip");
    break;

  default:
    echo "<div class'error'>Action '$action' inconnue</div>";
}

function extractFiles($schemaDir, $schemaFile) {
  $baseDir = "modules/sip/hprim";
  $destinationDir = "$baseDir/$schemaDir";
  $archivePath = "$baseDir/$schemaFile";
  if (false != $nbFiles = CMbPath::extract($archivePath, $destinationDir)) {
    echo "<div class='message'>Extraction de $nbFiles fichiers pour $schemaDir</div>";
  } else {
    echo "<div class='error'>Impossible d'extraire l'archive $schemaFile</div>";
  }
}

?>