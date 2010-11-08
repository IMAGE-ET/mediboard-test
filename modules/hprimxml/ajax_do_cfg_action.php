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

  case "evt_serveuretatspatient":
    if ($version == "1.05") {
      extractFiles("serveurActivitePmsi" , "schemaServeurActivitePmsi_v105.zip");
    }
    break;
    
  case "evt_frais_divers":
    if ($version == "1.05") {
      extractFiles("serveurActivitePmsi" , "schemaServeurActivitePmsi_v105.zip");
    }
    break;
    
  case "evt_patients":
  	$version = str_replace(".", "", $version);
    extractFiles("patients" , "schemaEvenementPatient_v$version.zip", true);
    break;
    
  case "evt_mvtStock":
    $version = str_replace(".", "", $version);
    extractFiles("mvtStock" , "schemaEvenementMvtStock_v$version.zip", true);
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
      echo "<div class='info'>Suppression de $destinationDir</div>";
    } else {
      echo "<div class='error'>Impossible de supprimer le dossier $destinationDir</div>";
      return;
    }
  }
  if (false != $nbFiles = CMbPath::extract($archivePath, $destinationDir)) {
    echo "<div class='info'>Extraction de $nbFiles fichiers pour $schemaDir</div>";
  } else {
    echo "<div class='error'>Impossible d'extraire l'archive $schemaFile</div>";
    return;
  }
  
  if(CAppUI::conf("hprimxml concatenate_xsd")) {
    $rootFiles = glob("$destinationDir/msg*.xsd");
    $includeFiles = array_diff(glob("$destinationDir/*.xsd"), $rootFiles);
    $importFiles = array();
    
    foreach($rootFiles as $rootFile) {
      $xsd = new DOMDOcument();
      $xsd->loadXML(file_get_contents($rootFile));
      
      // suppression des includes actuels
      $xpath = new DOMXPath($xsd);
      $includeNodes = $xpath->query("//xsd:include");
      foreach($includeNodes as $node) {
        $node->parentNode->removeChild($node);
      }
      
      // ... et des imports
      $includeNodes = $xpath->query("//xsd:import");
      foreach($includeNodes as $node) {
        $node->parentNode->removeChild($node);
      }
      
      foreach($includeFiles as $includeFile) {
        $include = new DOMDOcument();
        $include->loadXML(file_get_contents($includeFile));
        
        // suppression des includes actuels
        $xpath = new DOMXPath($xsd);
        $includeNodes = $xpath->query("//xsd:include");
        foreach($includeNodes as $node) {
          $node->parentNode->removeChild($node);
        }
        
        // ... et des imports
        $includeNodes = $xpath->query("//xsd:import[@schemaLocation]");
        foreach($includeNodes as $node) {
          $schemaLocation = $node->getAttribute("schemaLocation");
          $importFiles[$schemaLocation] = true;
          $node->removeAttribute("schemaLocation");
          //$node->parentNode->removeChild($node);
        }
        
        $isImport = false;
        foreach($importFiles as $key => $value) {
          if (strpos($includeFile, $key) !== false) {
            $isImport = true;
            break;
          }
        }
        
        foreach($include->documentElement->childNodes as $child) {
          $impNode = $xsd->importNode($child, true);
          
          if ($isImport) {
            $xsd->documentElement->setAttribute("xmlns:insee", "http://www.hprim.org/inseeXML");
            if (!$child instanceof DOMText && !$child instanceof DOMComment) {
              if ($name = $impNode->getAttribute("name")) {
                $name = "insee:$name";
                $impNode->setAttribute("name", $name);
              }
            }
          }
          
          $xsd->documentElement->appendChild($impNode);
        }
  
      }
      
      file_put_contents(substr($rootFile, 0, -4).".xml", $xsd->saveXML());
      
      echo "<div class='info'>Schéma concatené</div>";
    }
  }
}

?>