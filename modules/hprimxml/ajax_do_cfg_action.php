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
    
    foreach($rootFiles as $rootFile) {
      $xsd = new CMbXMLSchema();
      $xsd->loadXML(file_get_contents($rootFile));
      $xpath = new DOMXPath($xsd);
      
      $importFiles = array();
      
      foreach($includeFiles as $includeFile) {
        $include = new DOMDOcument();
        $include->loadXML(file_get_contents($includeFile));
        
        $isImport = false;
        foreach($importFiles as $key => $value) {
          if (strpos($includeFile, $key) !== false) {
            $isImport = true;
            break;
          }
        }
        
        foreach($include->documentElement->childNodes as $child) {
          $impNode = $xsd->importNode($child, true);
          
          $existing = false;
          
          if (in_array($impNode->nodeName, array("xsd:simpleType", "xsd:complexType"))){
            $name = $impNode->getAttribute('name');
            $existing = $xpath->query("//{$impNode->nodeName}[@name='$name']")->length > 0;
          }
          
          if ($isImport) {
            $xsd->documentElement->setAttribute("xmlns:insee", "http://www.hprim.org/inseeXML");
            
            /*if (!$impNode instanceof DOMText && !$impNode instanceof DOMComment) {
              $nodeName = "insee:".substr($impNode->nodeName, strlen("xsd:"));
              $newNode = $xsd->createElement($nodeName);
              
              foreach($impNode->attributes as $attr){
                $newNode->setAttribute($attr->name, $attr->value);
              }
              
              foreach($impNode->childNodes as $_childNode){
                //$_childNode = $_childNode->parentNode->removeChild($_childNode);
                $newNode->appendChild($_childNode);
              }
              
              $impNode = $newNode;
            }*/
          }
          
          if (!$existing)
            $xsd->documentElement->appendChild($impNode);
        }
  
      }
      
      /*$includeNodes = $xpath->query("//xsd:include");
      foreach($includeNodes as $node) {
        $node->parentNode->removeChild($node);
      }
      
      $includeNodes = $xpath->query("//xsd:import[@schemaLocation]");
      foreach($includeNodes as $node) {
        $schemaLocation = $node->getAttribute("schemaLocation");
        $alreadyImported = isset($importFiles[$schemaLocation]);
        
        $importFiles[$schemaLocation] = true;
        $node = $node->parentNode->removeChild($node);
        
        if (!$alreadyImported) {
          $xsd->documentElement->setAttribute("xmlns:insee", "http://www.hprim.org/inseeXML");
          $node->removeAttribute("schemaLocation");
          $xsd->documentElement->insertBefore($node, $xsd->documentElement->firstChild);
        }
      }*/
      
      $xsd->purgeImportedNamespaces();
      $xsd->purgeIncludes();
      
      file_put_contents(substr($rootFile, 0, -4).".xml", $xsd->saveXML());
      
      echo "<div class='info'>Schéma concatené</div>";
    }
  }
}

?>