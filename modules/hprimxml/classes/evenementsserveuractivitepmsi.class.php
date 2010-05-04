<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsServeurActivitePmsi extends CHPrimXMLDocument {
  static $evenements = array(
    'evenementPMSI'        => "CHPrimXMLEvenementsPmsi",
    'evenementServeurActe' => "CHPrimXMLEvenementsServeurActes",
  );
  
  function __construct($dirschemaname, $schemafilename = null) {
    $this->type = "pmsi";
    
    $version = CAppUI::conf("hprimxml $this->evenement version");
    if ($version == "1.01") {
      parent::__construct($dirschemaname, $schemafilename."101");
    } else if ($version == "1.05") {
      parent::__construct("serveurActivitePmsi", $schemafilename."105");
    }   
  }
  
  function getDateInterv($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    // Obligatoire pour MB
    $debut = $xpath->queryUniqueNode("hprim:debut", $node, false);
    
    return $xpath->queryTextNode("hprim:date", $debut);
  }
  
  function mappingActesCCAM($data) {
    $node = $data['actesCCAM'];
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $actesCCAM = array();
    foreach ($node->childNodes as $_acteCCAM) {
      $actesCCAM[] = $this->mappingActeCCAM($_acteCCAM, $data);
    }

    return $actesCCAM;
  }
  
  function mappingActeCCAM($node, $data) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
		$identifiant = $xpath->queryUniqueNode("hprim:identifiant", $data["intervention"]);
		    
    return array (
      "idSourceIntervention" => $xpath->queryUniqueNode("hprim:emetteur", $identifiant, false),
      "idCibleIntervention"  => $xpath->queryUniqueNode("hprim:recepteur", $identifiant),
      "idSourceActeCCAM"     => $xpath->queryTextNode("hprim:identifiant/hprim:emetteur", $node),
      "idCibleActeCCAM"      => $xpath->queryTextNode("hprim:identifiant/hprim:recepteur", $node),
      "codeActe"             => $xpath->queryTextNode("hprim:codeActe", $node),
      "codeActivite"         => $xpath->queryTextNode("hprim:codeActivite", $node),
      "codePhase"            => $xpath->queryTextNode("hprim:codePhase", $node),
      "executeDate"          => $xpath->queryTextNode("hprim:execute/hprim:date", $node),
      "executeHeure"         => mbTransformTime($xpath->queryTextNode("hprim:execute/hprim:heure", $node), null , "%H:%M:%S"),
    );
  }
}

?>